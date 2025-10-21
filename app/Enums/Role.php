<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Admin = 'Admin';
    case Manager = 'Manager';
    case SalesRepresentative = 'Sales Representative';
    case Support = 'Support';

    /**
     * Get all role values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }

    /**
     * Get permissions for this role.
     *
     * @return array<Permission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Admin => Permission::cases(),
            self::Manager => [
                Permission::ViewAnyCustomer,
                Permission::ViewCustomer,
                Permission::CreateCustomer,
                Permission::UpdateCustomer,
                Permission::ViewAnyOrder,
                Permission::ViewOrder,
                Permission::CreateOrder,
                Permission::UpdateOrder,
                Permission::ViewAnyInvoice,
                Permission::ViewInvoice,
                Permission::CreateInvoice,
                Permission::UpdateInvoice,
                Permission::ViewAnyOpportunity,
                Permission::ViewOpportunity,
                Permission::CreateOpportunity,
                Permission::UpdateOpportunity,
                Permission::ViewAnyQuote,
                Permission::ViewQuote,
                Permission::CreateQuote,
                Permission::UpdateQuote,
                Permission::ViewAnyCampaign,
                Permission::ViewCampaign,
                Permission::ViewAnyProduct,
                Permission::ViewProduct,
                ...Permission::tasks(),
                ...Permission::complaints(),
            ],
            self::SalesRepresentative => [
                Permission::ViewAnyCustomer,
                Permission::ViewCustomer,
                Permission::CreateCustomer,
                Permission::UpdateCustomer,
                Permission::ViewAnyOpportunity,
                Permission::ViewOpportunity,
                Permission::CreateOpportunity,
                Permission::UpdateOpportunity,
                Permission::ViewAnyQuote,
                Permission::ViewQuote,
                Permission::CreateQuote,
                Permission::UpdateQuote,
                Permission::ViewAnyOrder,
                Permission::ViewOrder,
                Permission::CreateOrder,
                Permission::UpdateOrder,
                Permission::ViewAnyProduct,
                Permission::ViewProduct,
                ...Permission::tasks(),
                ...Permission::interactions(),
            ],
            self::Support => [
                Permission::ViewAnyCustomer,
                Permission::ViewCustomer,
                ...Permission::complaints(),
                ...Permission::tasks(),
                ...Permission::interactions(),
            ],
        };
    }
}
