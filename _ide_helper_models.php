<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string $description
 * @property \App\Enums\ComplaintSeverity $severity
 * @property \App\Enums\BugReportStatus $status
 * @property string|null $source
 * @property int|null $assigned_to
 * @property \Carbon\CarbonImmutable|null $resolved_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User|null $assignedUser
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\BugReportFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BugReport whereUserId($value)
 */
	final class BugReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Carbon\CarbonImmutable $start_date
 * @property \Carbon\CarbonImmutable|null $end_date
 * @property string $status
 * @property array<array-key, mixed>|null $target_audience_criteria
 * @property int|null $created_by
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property \App\Enums\CampaignType $campaign_type
 * @property numeric|null $budget
 * @property numeric $actual_cost
 * @property int $clicks
 * @property int $impressions
 * @property string|null $google_ads_campaign_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CampaignConversion> $conversions
 * @property-read int|null $conversions_count
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GoogleAdsReport> $googleAdsReports
 * @property-read int|null $google_ads_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Opportunity> $opportunities
 * @property-read int|null $opportunities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CampaignResponse> $responses
 * @property-read int|null $responses_count
 * @property-read \App\Models\CampaignCustomer|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Customer> $targetAudience
 * @property-read int|null $target_audience_count
 * @method static \Database\Factories\CampaignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereActualCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereCampaignType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereGoogleAdsCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereTargetAudienceCriteria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withoutTrashed()
 */
	final class Campaign extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $campaign_id
 * @property int|null $customer_id
 * @property int|null $opportunity_id
 * @property \Carbon\CarbonImmutable $conversion_date
 * @property numeric $conversion_value
 * @property numeric $cost_at_conversion
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_term
 * @property string|null $utm_content
 * @property string|null $google_ads_conversion_id
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Campaign $campaign
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\Opportunity|null $opportunity
 * @method static \Database\Factories\CampaignConversionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereConversionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereConversionValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereCostAtConversion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereGoogleAdsConversionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereOpportunityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereUtmCampaign($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereUtmContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereUtmMedium($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereUtmSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignConversion whereUtmTerm($value)
 */
	final class CampaignConversion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $campaign_id
 * @property int $customer_id
 * @property \Carbon\CarbonImmutable $added_at
 * @property int|null $added_by
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User|null $addedBy
 * @property-read \App\Models\Campaign $campaign
 * @property-read \App\Models\Customer $customer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignCustomer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignCustomer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignCustomer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignCustomer whereAddedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignCustomer whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignCustomer whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignCustomer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignCustomer whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignCustomer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignCustomer whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignCustomer whereUpdatedAt($value)
 */
	final class CampaignCustomer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $campaign_id
 * @property int $customer_id
 * @property \App\Enums\CampaignResponseType $response_type
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $responded_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Campaign $campaign
 * @property-read \App\Models\Customer $customer
 * @method static \Database\Factories\CampaignResponseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignResponse whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignResponse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignResponse whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignResponse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignResponse whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignResponse whereRespondedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignResponse whereResponseType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignResponse whereUpdatedAt($value)
 */
	final class CampaignResponse extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $chat_session_id
 * @property \App\Enums\ChatMessageSenderType $sender_type
 * @property int|null $sender_id
 * @property string $message
 * @property bool $is_read
 * @property \Carbon\CarbonImmutable|null $read_at
 * @property int|null $parent_message_id
 * @property \Carbon\CarbonImmutable|null $edited_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\ChatSession $chatSession
 * @property-read ChatMessage|null $parentMessage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ChatMessage> $replies
 * @property-read int|null $replies_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $sender
 * @method static \Database\Factories\ChatMessageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereChatSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereEditedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereParentMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereSenderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage withoutTrashed()
 */
	final class ChatMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $customer_id
 * @property int|null $user_id
 * @property \Carbon\CarbonImmutable $started_at
 * @property \Carbon\CarbonImmutable|null $ended_at
 * @property \App\Enums\ChatSessionStatus $status
 * @property \Carbon\CarbonImmutable|null $last_message_at
 * @property int $unread_count
 * @property string $priority
 * @property int|null $rating
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\ChatSessionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereLastMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereUnreadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereUserId($value)
 */
	final class ChatSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $customer_id
 * @property \App\Enums\CommunicationChannel $channel
 * @property \App\Enums\CommunicationDirection $direction
 * @property string|null $subject
 * @property string $content
 * @property \App\Enums\CommunicationStatus $status
 * @property \Carbon\CarbonImmutable|null $sent_at
 * @property \Carbon\CarbonImmutable|null $delivered_at
 * @property \Carbon\CarbonImmutable|null $read_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Customer|null $customer
 * @method static \Database\Factories\CommunicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Communication whereUpdatedAt($value)
 */
	final class Communication extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property int|null $order_id
 * @property int|null $reported_by
 * @property int|null $assigned_to
 * @property string $title
 * @property string $description
 * @property \App\Enums\ComplaintSeverity $severity
 * @property \App\Enums\ComplaintStatus $status
 * @property string|null $resolution
 * @property \Carbon\CarbonImmutable $reported_at
 * @property \Carbon\CarbonImmutable|null $resolved_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User|null $assignedUser
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ComplaintEscalation> $escalations
 * @property-read int|null $escalations_count
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $reporter
 * @method static \Database\Factories\ComplaintFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereReportedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereReportedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereUpdatedAt($value)
 */
	final class Complaint extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $complaint_id
 * @property int $escalated_to
 * @property int $escalated_by
 * @property string $reason
 * @property \Carbon\CarbonImmutable $escalated_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Complaint $complaint
 * @property-read \App\Models\User $escalatedByUser
 * @property-read \App\Models\User $escalatedToUser
 * @method static \Database\Factories\ComplaintEscalationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplaintEscalation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplaintEscalation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplaintEscalation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplaintEscalation whereComplaintId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplaintEscalation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplaintEscalation whereEscalatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplaintEscalation whereEscalatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplaintEscalation whereEscalatedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplaintEscalation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplaintEscalation whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplaintEscalation whereUpdatedAt($value)
 */
	final class ComplaintEscalation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $unique_identifier
 * @property string $name
 * @property \App\Enums\CustomerType $type
 * @property string|null $tax_number
 * @property string|null $registration_number
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $notes
 * @property bool $is_active
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerAddress> $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerAttribute> $attributes
 * @property-read int|null $attributes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatSession> $chatSessions
 * @property-read int|null $chat_sessions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Communication> $communications
 * @property-read int|null $communications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Complaint> $complaints
 * @property-read int|null $complaints_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerContact> $contacts
 * @property-read int|null $contacts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Discount> $discounts
 * @property-read int|null $discounts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Opportunity> $opportunities
 * @property-read int|null $opportunities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Quote> $quotes
 * @property-read int|null $quotes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Shipment> $shipments
 * @property-read int|null $shipments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Database\Factories\CustomerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereRegistrationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereTaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUniqueIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withoutTrashed()
 */
	final class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property string $type
 * @property string $country
 * @property string $postal_code
 * @property string $city
 * @property string $street
 * @property string|null $building_number
 * @property string|null $floor
 * @property string|null $door
 * @property bool $is_default
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @method static \Database\Factories\CustomerAddressFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress whereBuildingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress whereDoor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress whereFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAddress whereUpdatedAt($value)
 */
	final class CustomerAddress extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property string $attribute_key
 * @property string|null $attribute_value
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @method static \Database\Factories\CustomerAttributeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAttribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAttribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAttribute query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAttribute whereAttributeKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAttribute whereAttributeValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAttribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAttribute whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAttribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAttribute whereUpdatedAt($value)
 */
	final class CustomerAttribute extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $position
 * @property bool $is_primary
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @method static \Database\Factories\CustomerContactFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerContact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerContact whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerContact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerContact whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerContact whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerContact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerContact wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerContact whereUpdatedAt($value)
 */
	final class CustomerContact extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \App\Enums\DiscountType $type
 * @property \App\Enums\DiscountValueType $value_type
 * @property numeric $value
 * @property numeric|null $min_quantity
 * @property numeric|null $min_value
 * @property \Carbon\CarbonImmutable|null $valid_from
 * @property \Carbon\CarbonImmutable|null $valid_until
 * @property int|null $customer_id
 * @property int|null $product_id
 * @property bool $is_active
 * @property string|null $description
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\Product|null $product
 * @method static \Database\Factories\DiscountFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereMinQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereMinValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereValidUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereValueType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount withoutTrashed()
 */
	final class Discount extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $campaign_id
 * @property \Carbon\CarbonImmutable $report_date
 * @property array<array-key, mixed>|null $metadata
 * @property array<array-key, mixed>|null $keyword_data
 * @property array<array-key, mixed>|null $historical_metrics
 * @property array<array-key, mixed>|null $bulk_results
 * @property array<array-key, mixed>|null $statistics
 * @property array<array-key, mixed>|null $raw_data
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\Campaign $campaign
 * @method static \Database\Factories\GoogleAdsReportFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport whereBulkResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport whereHistoricalMetrics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport whereKeywordData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport whereRawData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport whereReportDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport whereStatistics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleAdsReport withoutTrashed()
 */
	final class GoogleAdsReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property int $user_id
 * @property \App\Enums\InteractionType $type
 * @property string $subject
 * @property string|null $description
 * @property \Carbon\CarbonImmutable $interaction_date
 * @property int|null $duration
 * @property string|null $next_action
 * @property \Carbon\CarbonImmutable|null $next_action_date
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\InteractionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction whereInteractionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction whereNextAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction whereNextActionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction whereUserId($value)
 */
	final class Interaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property int|null $order_id
 * @property string $invoice_number
 * @property \Carbon\CarbonImmutable $issue_date
 * @property \Carbon\CarbonImmutable $due_date
 * @property \App\Enums\InvoiceStatus $status
 * @property numeric $subtotal
 * @property numeric $discount_amount
 * @property numeric $tax_amount
 * @property numeric $total
 * @property string|null $notes
 * @property array<array-key, mixed>|null $files
 * @property \Carbon\CarbonImmutable|null $paid_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\Order|null $order
 * @method static \Database\Factories\InvoiceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereFiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice withoutTrashed()
 */
	final class Invoice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property string $title
 * @property string|null $description
 * @property numeric|null $value
 * @property int $probability
 * @property \App\Enums\OpportunityStage $stage
 * @property \Carbon\CarbonImmutable|null $expected_close_date
 * @property int|null $assigned_to
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property int|null $campaign_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User|null $assignedUser
 * @property-read \App\Models\Campaign|null $campaign
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Quote> $quotes
 * @property-read int|null $quotes_count
 * @method static \Database\Factories\OpportunityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereExpectedCloseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereProbability($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opportunity withoutTrashed()
 */
	final class Opportunity extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property int|null $quote_id
 * @property string $order_number
 * @property \Carbon\CarbonImmutable $order_date
 * @property \App\Enums\OrderStatus $status
 * @property numeric $subtotal
 * @property numeric $discount_amount
 * @property numeric $tax_amount
 * @property numeric $total
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \App\Models\Quote|null $quote
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Shipment> $shipments
 * @property-read int|null $shipments_count
 * @method static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereQuoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order withoutTrashed()
 */
	final class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property string|null $description
 * @property numeric $quantity
 * @property numeric $unit_price
 * @property numeric $discount_amount
 * @property numeric $tax_rate
 * @property numeric $total
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product|null $product
 * @method static \Database\Factories\OrderItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
 */
	final class OrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $sku
 * @property string|null $description
 * @property int|null $category_id
 * @property numeric $unit_price
 * @property numeric $tax_rate
 * @property bool $is_active
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\ProductCategory|null $category
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 */
	final class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int|null $parent_id
 * @property string|null $description
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductCategory> $children
 * @property-read int|null $children_count
 * @property-read ProductCategory|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Database\Factories\ProductCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereUpdatedAt($value)
 */
	final class ProductCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property int|null $opportunity_id
 * @property string $quote_number
 * @property \Carbon\CarbonImmutable $issue_date
 * @property \Carbon\CarbonImmutable $valid_until
 * @property \App\Enums\QuoteStatus $status
 * @property numeric $subtotal
 * @property numeric $discount_amount
 * @property numeric $tax_amount
 * @property numeric $total
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuoteItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Opportunity|null $opportunity
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @method static \Database\Factories\QuoteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereOpportunityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereQuoteNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereValidUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote withoutTrashed()
 */
	final class Quote extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $quote_id
 * @property int|null $product_id
 * @property string $description
 * @property numeric $quantity
 * @property numeric $unit_price
 * @property numeric $discount_percent
 * @property numeric $discount_amount
 * @property numeric $tax_rate
 * @property numeric $total
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Quote $quote
 * @method static \Database\Factories\QuoteItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem whereQuoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuoteItem whereUpdatedAt($value)
 */
	final class QuoteItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $customer_id
 * @property int|null $order_id
 * @property string|null $external_customer_id
 * @property string|null $external_order_id
 * @property string $shipment_number
 * @property string|null $carrier
 * @property string|null $tracking_number
 * @property \App\Enums\ShipmentStatus $status
 * @property array<array-key, mixed>|null $shipping_address
 * @property \Carbon\CarbonImmutable|null $shipped_at
 * @property \Carbon\CarbonImmutable|null $estimated_delivery_at
 * @property \Carbon\CarbonImmutable|null $delivered_at
 * @property string|null $notes
 * @property array<array-key, mixed>|null $documents
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShipmentItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Order|null $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShipmentTrackingEvent> $trackingEvents
 * @property-read int|null $tracking_events_count
 * @method static \Database\Factories\ShipmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereCarrier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereEstimatedDeliveryAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereExternalCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereExternalOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereShipmentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereShippedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereShippingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereTrackingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment withoutTrashed()
 */
	final class Shipment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $shipment_id
 * @property int|null $order_item_id
 * @property string|null $external_product_id
 * @property string|null $product_name
 * @property string|null $product_sku
 * @property int $quantity
 * @property numeric|null $weight
 * @property numeric|null $length
 * @property numeric|null $width
 * @property numeric|null $height
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\OrderItem|null $orderItem
 * @property-read \App\Models\Shipment $shipment
 * @method static \Database\Factories\ShipmentItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereExternalProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereOrderItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereProductSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereShipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentItem whereWidth($value)
 */
	final class ShipmentItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $shipment_id
 * @property string $status_code
 * @property string|null $location
 * @property string|null $description
 * @property \Carbon\CarbonImmutable $occurred_at
 * @property array<array-key, mixed>|null $metadata
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Shipment $shipment
 * @method static \Database\Factories\ShipmentTrackingEventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentTrackingEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentTrackingEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentTrackingEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentTrackingEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentTrackingEvent whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentTrackingEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentTrackingEvent whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentTrackingEvent whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentTrackingEvent whereOccurredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentTrackingEvent whereShipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentTrackingEvent whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShipmentTrackingEvent whereUpdatedAt($value)
 */
	final class ShipmentTrackingEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $customer_id
 * @property int $assigned_to
 * @property int $assigned_by
 * @property string $title
 * @property string|null $description
 * @property string $priority
 * @property string $status
 * @property \Carbon\CarbonImmutable|null $due_date
 * @property \Carbon\CarbonImmutable|null $completed_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $assignedUser
 * @property-read \App\Models\User $assigner
 * @property-read \App\Models\Customer|null $customer
 * @method static \Database\Factories\TaskFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereAssignedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 */
	final class Task extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property bool $is_online
 * @property \Carbon\CarbonImmutable|null $last_seen_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BugReport> $bugReports
 * @property-read int|null $bug_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatSession> $chatSessions
 * @property-read int|null $chat_sessions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	final class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser {}
}

