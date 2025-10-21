<?php

declare(strict_types=1);

namespace App\Enums;

enum Permission: string
{
    // Customer permissions
    case ViewAnyCustomer = 'view_any_customer';
    case ViewCustomer = 'view_customer';
    case CreateCustomer = 'create_customer';
    case UpdateCustomer = 'update_customer';
    case DeleteCustomer = 'delete_customer';
    case RestoreCustomer = 'restore_customer';
    case ForceDeleteCustomer = 'force_delete_customer';

    // Campaign permissions
    case ViewAnyCampaign = 'view_any_campaign';
    case ViewCampaign = 'view_campaign';
    case CreateCampaign = 'create_campaign';
    case UpdateCampaign = 'update_campaign';
    case DeleteCampaign = 'delete_campaign';
    case RestoreCampaign = 'restore_campaign';
    case ForceDeleteCampaign = 'force_delete_campaign';

    // Opportunity permissions
    case ViewAnyOpportunity = 'view_any_opportunity';
    case ViewOpportunity = 'view_opportunity';
    case CreateOpportunity = 'create_opportunity';
    case UpdateOpportunity = 'update_opportunity';
    case DeleteOpportunity = 'delete_opportunity';
    case RestoreOpportunity = 'restore_opportunity';
    case ForceDeleteOpportunity = 'force_delete_opportunity';

    // Quote permissions
    case ViewAnyQuote = 'view_any_quote';
    case ViewQuote = 'view_quote';
    case CreateQuote = 'create_quote';
    case UpdateQuote = 'update_quote';
    case DeleteQuote = 'delete_quote';
    case RestoreQuote = 'restore_quote';
    case ForceDeleteQuote = 'force_delete_quote';

    // Order permissions
    case ViewAnyOrder = 'view_any_order';
    case ViewOrder = 'view_order';
    case CreateOrder = 'create_order';
    case UpdateOrder = 'update_order';
    case DeleteOrder = 'delete_order';
    case RestoreOrder = 'restore_order';
    case ForceDeleteOrder = 'force_delete_order';

    // Invoice permissions
    case ViewAnyInvoice = 'view_any_invoice';
    case ViewInvoice = 'view_invoice';
    case CreateInvoice = 'create_invoice';
    case UpdateInvoice = 'update_invoice';
    case DeleteInvoice = 'delete_invoice';
    case RestoreInvoice = 'restore_invoice';
    case ForceDeleteInvoice = 'force_delete_invoice';

    // Product permissions
    case ViewAnyProduct = 'view_any_product';
    case ViewProduct = 'view_product';
    case CreateProduct = 'create_product';
    case UpdateProduct = 'update_product';
    case DeleteProduct = 'delete_product';
    case RestoreProduct = 'restore_product';
    case ForceDeleteProduct = 'force_delete_product';

    // Task permissions
    case ViewAnyTask = 'view_any_task';
    case ViewTask = 'view_task';
    case CreateTask = 'create_task';
    case UpdateTask = 'update_task';
    case DeleteTask = 'delete_task';
    case RestoreTask = 'restore_task';
    case ForceDeleteTask = 'force_delete_task';

    // Complaint permissions
    case ViewAnyComplaint = 'view_any_complaint';
    case ViewComplaint = 'view_complaint';
    case CreateComplaint = 'create_complaint';
    case UpdateComplaint = 'update_complaint';
    case DeleteComplaint = 'delete_complaint';
    case RestoreComplaint = 'restore_complaint';
    case ForceDeleteComplaint = 'force_delete_complaint';

    // Interaction permissions
    case ViewAnyInteraction = 'view_any_interaction';
    case ViewInteraction = 'view_interaction';
    case CreateInteraction = 'create_interaction';
    case UpdateInteraction = 'update_interaction';
    case DeleteInteraction = 'delete_interaction';
    case RestoreInteraction = 'restore_interaction';
    case ForceDeleteInteraction = 'force_delete_interaction';

    /**
     * Get all permissions for a specific resource.
     *
     * @return array<self>
     */
    public static function forResource(string $resource): array
    {
        return array_filter(
            self::cases(),
            fn (self $permission): bool => str_ends_with($permission->value, mb_strtolower($resource))
        );
    }

    /**
     * Get all permission values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $permission) => $permission->value, self::cases());
    }

    /**
     * Get all customer permissions.
     *
     * @return array<self>
     */
    public static function customers(): array
    {
        return [
            self::ViewAnyCustomer,
            self::ViewCustomer,
            self::CreateCustomer,
            self::UpdateCustomer,
            self::DeleteCustomer,
            self::RestoreCustomer,
            self::ForceDeleteCustomer,
        ];
    }

    /**
     * Get all campaign permissions.
     *
     * @return array<self>
     */
    public static function campaigns(): array
    {
        return [
            self::ViewAnyCampaign,
            self::ViewCampaign,
            self::CreateCampaign,
            self::UpdateCampaign,
            self::DeleteCampaign,
            self::RestoreCampaign,
            self::ForceDeleteCampaign,
        ];
    }

    /**
     * Get all opportunity permissions.
     *
     * @return array<self>
     */
    public static function opportunities(): array
    {
        return [
            self::ViewAnyOpportunity,
            self::ViewOpportunity,
            self::CreateOpportunity,
            self::UpdateOpportunity,
            self::DeleteOpportunity,
            self::RestoreOpportunity,
            self::ForceDeleteOpportunity,
        ];
    }

    /**
     * Get all quote permissions.
     *
     * @return array<self>
     */
    public static function quotes(): array
    {
        return [
            self::ViewAnyQuote,
            self::ViewQuote,
            self::CreateQuote,
            self::UpdateQuote,
            self::DeleteQuote,
            self::RestoreQuote,
            self::ForceDeleteQuote,
        ];
    }

    /**
     * Get all order permissions.
     *
     * @return array<self>
     */
    public static function orders(): array
    {
        return [
            self::ViewAnyOrder,
            self::ViewOrder,
            self::CreateOrder,
            self::UpdateOrder,
            self::DeleteOrder,
            self::RestoreOrder,
            self::ForceDeleteOrder,
        ];
    }

    /**
     * Get all invoice permissions.
     *
     * @return array<self>
     */
    public static function invoices(): array
    {
        return [
            self::ViewAnyInvoice,
            self::ViewInvoice,
            self::CreateInvoice,
            self::UpdateInvoice,
            self::DeleteInvoice,
            self::RestoreInvoice,
            self::ForceDeleteInvoice,
        ];
    }

    /**
     * Get all product permissions.
     *
     * @return array<self>
     */
    public static function products(): array
    {
        return [
            self::ViewAnyProduct,
            self::ViewProduct,
            self::CreateProduct,
            self::UpdateProduct,
            self::DeleteProduct,
            self::RestoreProduct,
            self::ForceDeleteProduct,
        ];
    }

    /**
     * Get all task permissions.
     *
     * @return array<self>
     */
    public static function tasks(): array
    {
        return [
            self::ViewAnyTask,
            self::ViewTask,
            self::CreateTask,
            self::UpdateTask,
            self::DeleteTask,
            self::RestoreTask,
            self::ForceDeleteTask,
        ];
    }

    /**
     * Get all complaint permissions.
     *
     * @return array<self>
     */
    public static function complaints(): array
    {
        return [
            self::ViewAnyComplaint,
            self::ViewComplaint,
            self::CreateComplaint,
            self::UpdateComplaint,
            self::DeleteComplaint,
            self::RestoreComplaint,
            self::ForceDeleteComplaint,
        ];
    }

    /**
     * Get all interaction permissions.
     *
     * @return array<self>
     */
    public static function interactions(): array
    {
        return [
            self::ViewAnyInteraction,
            self::ViewInteraction,
            self::CreateInteraction,
            self::UpdateInteraction,
            self::DeleteInteraction,
            self::RestoreInteraction,
            self::ForceDeleteInteraction,
        ];
    }
}
