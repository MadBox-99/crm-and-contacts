<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Models\Communication;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;
use Webklex\IMAP\Facades\Client as ImapClient;

final class EmailIntegrationService
{
    /**
     * Fetch new inbound emails from IMAP and store them as Communications.
     *
     * @return Collection<int, Communication>
     */
    public function fetchInboundEmails(int $teamId, string $account = 'default'): Collection
    {
        $stored = collect();

        try {
            $client = ImapClient::account($account);
            $client->connect();

            $folder = $client->getFolder('INBOX');

            if (! $folder) {
                Log::warning('EmailIntegration: INBOX folder not found', ['account' => $account]);

                return $stored;
            }

            $messages = $folder->query()->unseen()->get();

            foreach ($messages as $message) {
                $fromEmail = $message->getFrom()[0]?->mail ?? '';
                $subject = (string) $message->getSubject();
                $body = $message->getTextBody() ?: strip_tags((string) $message->getHTMLBody());
                $messageId = (string) $message->getMessageId();
                $inReplyTo = $message->getInReplyTo() ? (string) $message->getInReplyTo() : null;

                $toAddresses = collect($message->getTo())->pluck('mail')->filter()->all();
                $ccAddresses = collect($message->getCc())->pluck('mail')->filter()->all();

                $customer = $this->matchCustomerByEmail($fromEmail, $teamId);

                $threadId = $this->resolveThreadId($messageId, $inReplyTo, $teamId);

                $communication = Communication::query()->create([
                    'team_id' => $teamId,
                    'customer_id' => $customer?->id,
                    'channel' => CommunicationChannel::Email,
                    'direction' => CommunicationDirection::Inbound,
                    'subject' => $subject,
                    'content' => Str::limit($body, 65535),
                    'message_id' => $messageId,
                    'in_reply_to' => $inReplyTo,
                    'thread_id' => $threadId,
                    'from_email' => $fromEmail,
                    'to_email' => implode(', ', $toAddresses),
                    'cc' => $ccAddresses ?: null,
                    'has_attachments' => $message->hasAttachments(),
                    'status' => CommunicationStatus::Delivered,
                    'delivered_at' => $message->getDate()?->toDate() ?? now(),
                ]);

                $stored->push($communication);

                $message->setFlag('Seen');
            }

            $client->disconnect();
        } catch (Throwable $throwable) {
            Log::error('EmailIntegration: Failed to fetch emails', [
                'error' => $throwable->getMessage(),
                'account' => $account,
            ]);
        }

        return $stored;
    }

    /**
     * Send an outbound email and record it as a Communication.
     */
    public function sendEmail(
        int $teamId,
        string $toEmail,
        string $subject,
        string $body,
        ?int $customerId = null,
        ?string $replyToMessageId = null,
    ): Communication {
        $messageId = Str::uuid()->toString().'@'.config('app.url', 'crm.local');

        $threadId = $replyToMessageId
            ? $this->resolveThreadId($messageId, $replyToMessageId, $teamId)
            : $messageId;

        Mail::raw($body, function ($message) use ($toEmail, $subject, $messageId, $replyToMessageId): void {
            $message->to($toEmail)
                ->subject($subject);

            $message->getHeaders()->addTextHeader('Message-ID', '<'.$messageId.'>');

            if ($replyToMessageId) {
                $message->getHeaders()->addTextHeader('In-Reply-To', '<'.$replyToMessageId.'>');
                $message->getHeaders()->addTextHeader('References', '<'.$replyToMessageId.'>');
            }
        });

        return Communication::query()->create([
            'team_id' => $teamId,
            'customer_id' => $customerId,
            'channel' => CommunicationChannel::Email,
            'direction' => CommunicationDirection::Outbound,
            'subject' => $subject,
            'content' => $body,
            'message_id' => $messageId,
            'in_reply_to' => $replyToMessageId,
            'thread_id' => $threadId,
            'from_email' => config('mail.from.address'),
            'to_email' => $toEmail,
            'status' => CommunicationStatus::Sent,
            'sent_at' => now(),
        ]);
    }

    /**
     * Get a full email thread by thread ID.
     *
     * @return Collection<int, Communication>
     */
    public function getThread(string $threadId, int $teamId): Collection
    {
        return Communication::query()
            ->where('team_id', $teamId)
            ->where('thread_id', $threadId)->oldest()
            ->get();
    }

    /**
     * Get all email threads for a customer.
     *
     * @return Collection<int, array{thread_id: string, subject: string|null, message_count: int, last_at: string}>
     */
    public function getCustomerThreads(int $customerId, int $teamId): Collection
    {
        return Communication::query()
            ->where('team_id', $teamId)
            ->where('customer_id', $customerId)
            ->where('channel', CommunicationChannel::Email)
            ->whereNotNull('thread_id')
            ->selectRaw('thread_id, MIN(subject) as subject, COUNT(*) as message_count, MAX(created_at) as last_at')
            ->groupBy('thread_id')
            ->latest('last_at')
            ->get();
    }

    /**
     * Match an email address to an existing customer.
     */
    private function matchCustomerByEmail(string $email, int $teamId): ?Customer
    {
        return Customer::query()
            ->where('team_id', $teamId)
            ->where('email', $email)
            ->first();
    }

    /**
     * Resolve or create a thread ID based on message references.
     */
    private function resolveThreadId(string $messageId, ?string $inReplyTo, int $teamId): string
    {
        if ($inReplyTo) {
            $parent = Communication::query()
                ->where('team_id', $teamId)
                ->where('message_id', $inReplyTo)
                ->first();

            if ($parent && $parent->thread_id) {
                return $parent->thread_id;
            }
        }

        return $messageId;
    }
}
