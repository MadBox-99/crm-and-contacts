<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ComplaintSeverity;
use App\Enums\ComplaintStatus;
use App\Enums\CustomerType;
use App\Enums\Role;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\NewComplaintNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ComplaintSubmission extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string|min:10')]
    public string $description = '';

    #[Validate('nullable|string|max:255')]
    public string $order_number = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $this->validate();

        // Find or create customer
        $customer = Customer::query()
            ->where('email', $this->email)
            ->first();

        if (! $customer) {
            $customer = Customer::query()->create([
                'unique_identifier' => 'GUEST-'.now()->format('YmdHis'),
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'type' => CustomerType::B2C,
                'is_active' => true,
            ]);
        }

        // Create complaint (severity will be set by admin/manager later)
        $complaint = Complaint::query()->create([
            'customer_id' => $customer->id,
            'title' => $this->title,
            'description' => $this->description,
            'severity' => ComplaintSeverity::Medium,
            'status' => ComplaintStatus::Open,
            'reported_at' => now(),
        ]);

        // Notify admins and managers
        $this->notifyAdmins($complaint);

        $this->submitted = true;

        // Reset form
        $this->reset(['name', 'email', 'phone', 'title', 'description', 'order_number']);
    }

    public function render(): Factory|View
    {
        return view('livewire.complaint-submission');
    }

    private function notifyAdmins(Complaint $complaint): void
    {
        // Get all users with admin or manager role
        $adminsAndManagers = User::query()
            ->role([Role::Admin, Role::Manager])
            ->get();

        if ($adminsAndManagers->isNotEmpty()) {
            Notification::send($adminsAndManagers, new NewComplaintNotification($complaint));
        }
    }
}
