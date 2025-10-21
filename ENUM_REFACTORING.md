# Enum-based Permission & Role System Refactoring

## Áttekintés

A jogosultságkezelési rendszert refaktoráltuk, hogy típusbiztos enum-okat használjon a "magic string" értékek helyett. Ez javítja a kód minőségét, csökkenti a hibalehetőségeket és jobb IDE támogatást biztosít.

## Létrehozott Enum-ok

### 1. Permission Enum ([app/Enums/Permission.php](app/Enums/Permission.php))

Minden erőforrás (Customer, Campaign, Opportunity, Quote, Order, Invoice, Product, Task, Complaint, Interaction) számára definiált jogosultságok:

```php
enum Permission: string
{
    case ViewAnyCustomer = 'view_any_customer';
    case ViewCustomer = 'view_customer';
    case CreateCustomer = 'create_customer';
    case UpdateCustomer = 'update_customer';
    case DeleteCustomer = 'delete_customer';
    case RestoreCustomer = 'restore_customer';
    case ForceDeleteCustomer = 'force_delete_customer';
    // ... további erőforrások
}
```

**Helper metódusok:**
- `forResource(string $resource)` - Egy adott erőforrás összes jogosultsága
- `values()` - Összes jogosultság string értéke
- `customers()`, `campaigns()`, stb. - Erőforrás specifikus jogosultságok

### 2. Role Enum ([app/Enums/Role.php](app/Enums/Role.php))

```php
enum Role: string
{
    case Admin = 'Admin';
    case Manager = 'Manager';
    case SalesRepresentative = 'Sales Representative';
    case Support = 'Support';
}
```

**Beépített jogosultságkezelés:**
- `permissions()` - Adott szerephez tartozó jogosultságok listája
- `values()` - Összes szerep string értéke

#### Szerepkör jogosultságok:

**Admin:**
- Minden jogosultság

**Manager:**
- Customer, Order, Invoice, Opportunity, Quote: view, create, update (DE NEM delete!)
- Campaign: view only
- Product: view only
- Task, Complaint: teljes körű hozzáférés

**Sales Representative:**
- Customer, Opportunity, Quote, Order: view, create, update (DE NEM delete!)
- Product: view only
- Task, Interaction: teljes körű hozzáférés

**Support:**
- Customer: view only
- Complaint, Task, Interaction: teljes körű hozzáférés

## Frissített Fájlok

### Policy-k

Minden policy frissítve lett, hogy közvetlenül enum-okat használjon:

```php
// Előtte:
return $user->can('view_any_customer');

// Utána:
return $user->can(Permission::ViewAnyCustomer);
```

**Frissített policy-k:**
- [CustomerPolicy.php](app/Policies/CustomerPolicy.php)
- [CampaignPolicy.php](app/Policies/CampaignPolicy.php)
- [OpportunityPolicy.php](app/Policies/OpportunityPolicy.php)
- [QuotePolicy.php](app/Policies/QuotePolicy.php)
- [OrderPolicy.php](app/Policies/OrderPolicy.php)
- [InvoicePolicy.php](app/Policies/InvoicePolicy.php)
- [ProductPolicy.php](app/Policies/ProductPolicy.php)
- [TaskPolicy.php](app/Policies/TaskPolicy.php)
- [ComplaintPolicy.php](app/Policies/ComplaintPolicy.php)
- [InteractionPolicy.php](app/Policies/InteractionPolicy.php)

### PermissionSeeder

Teljesen újraírva, hogy enum-okat használjon:

```php
// Előtte: manual permission list
$resources = ['customer', 'order', ...];
$actions = ['view_any', 'view', ...];
foreach ($resources as $resource) {
    foreach ($actions as $action) {
        Permission::firstOrCreate(['name' => "{$action}_{$resource}"]);
    }
}

// Utána: enum-based
foreach (PermissionEnum::cases() as $permission) {
    Permission::firstOrCreate(['name' => $permission]);
}

foreach (RoleEnum::cases() as $roleEnum) {
    $role = Role::firstOrCreate(['name' => $roleEnum]);
    $role->syncPermissions($roleEnum->permissions());
}
```

### User Model

Kibővített custom metódusokkal, amelyek támogatják az enum paramétereket:

```php
public function hasRole(RoleEnum $role): bool
public function hasAnyRole(array $roles): bool
public function hasAllRoles(array $roles): bool
public function assignRole(RoleEnum|string ...$roles): static
public function givePermissionTo(mixed ...$permissions): static
```

Ezek a metódusok automatikusan konvertálják az enum-okat string értékekké a Spatie Permission package számára.

### Tesztek

Minden teszt frissítve lett, hogy enum-okat használjon:

```php
// Előtte:
Permission::create(['name' => 'view_any_customer']);
$user->givePermissionTo('view_any_customer');
$user->assignRole('Admin');

// Utána:
Permission::create(['name' => PermissionEnum::ViewAnyCustomer]);
$user->givePermissionTo(PermissionEnum::ViewAnyCustomer);
$user->assignRole(RoleEnum::Admin);
```

## Használat

### Policy-kban

```php
public function viewAny(User $user): bool
{
    return $user->can(Permission::ViewAnyCustomer);
}
```

### Controller-ekben / Request-ekben

```php
// Jogosultság ellenőrzés
$this->authorize(Permission::ViewCustomer, $customer);

// Szerepkör ellenőrzés
if ($user->hasRole(RoleEnum::Admin)) {
    // ...
}

// Többszörös szerepkör ellenőrzés
if ($user->hasAnyRole([RoleEnum::Admin, RoleEnum::Manager])) {
    // ...
}
```

### Seeder-ekben

```php
// Felhasználó létrehozása szerepkörrel
$user = User::factory()->create();
$user->assignRole(RoleEnum::Manager);

// Jogosultság hozzárendelése
$user->givePermissionTo(PermissionEnum::ViewAnyCustomer);
```

### Tesztekben

```php
// Permission létrehozása
Permission::create(['name' => PermissionEnum::ViewCustomer]);

// Szerepkör hozzárendelése
$user->assignRole(RoleEnum::SalesRepresentative);

// Jogosultság ellenőrzése
expect($user->can('view', $customer))->toBeTrue();
```

## Előnyök

### 1. Típusbiztonság

```php
// Hibás írásmód fordítási időben észlelhető
$user->can(Permission::ViewAnyCustomr); // ❌ Typo - IDE jelzi
$user->can(Permission::ViewAnyCustomer); // ✅ Helyes
```

### 2. IDE Támogatás

- Automatikus kiegészítés (IntelliSense)
- "Go to definition" funkció
- Refaktorálás támogatás
- Használati helyek megtalálása

### 3. Karbantarthatóság

- Központosított jogosultság definíció
- Nem lehet elírni a jogosultság nevét
- Könnyebb átnevezés és refaktorálás
- Dokumentáció közvetlenül a kódban

### 4. Szerepkör-alapú Jogosultságkezelés

A Role enum `permissions()` metódusa egyetlen helyen definiálja az összes szerepkörhöz tartozó jogosultságot, így:
- Egyszerűbb a szerepkörök kezelése
- Átláthatóbb a jogosultságok kiosztása
- Könnyebb új szerepkört létrehozni

## Kompatibilitás

A refaktorálás **100%-ban visszafelé kompatibilis** a Spatie Laravel Permission v6+ csomaggal, amely natívan támogatja a PHP 8.1+ enum-okat.

A package automatikusan kezeli az enum-string konverziót, így:
```php
// Mindkettő működik:
$user->can(Permission::ViewCustomer);
$user->can('view_customer');
```

## Tesztek

Minden teszt (70/70) sikeresen lefutott az enum-alapú refaktorálás után:

```bash
php artisan test
# Tests:  70 passed (218 assertions)
```

## Következő Lépések

Ha új erőforrást adsz hozzá (pl. "Contract"), akkor:

1. **Hozd létre a Permission enum eseteket** ([app/Enums/Permission.php](app/Enums/Permission.php)):
   ```php
   case ViewAnyContract = 'view_any_contract';
   case ViewContract = 'view_contract';
   // ... további jogosultságok
   ```

2. **Adj hozzá helper metódust**:
   ```php
   public static function contracts(): array
   {
       return [
           self::ViewAnyContract,
           self::ViewContract,
           // ...
       ];
   }
   ```

3. **Frissítsd a Role jogosultságokat** szükség szerint ([app/Enums/Role.php](app/Enums/Role.php))

4. **Futtasd le a seeder-t**:
   ```bash
   php artisan db:seed --class=PermissionSeeder
   ```

5. **Hozd létre a Policy-t** az enum-okat használva

6. **Írj teszteket** az új jogosultságokra

## Konklúzió

Az enum-alapú jogosultságkezelési rendszer jelentősen javítja a kód minőségét, biztonságát és karbantarthatóságát. A típusbiztonság és az IDE támogatás révén kevesebb hiba kerülhet a kódba, és a fejlesztés is gyorsabb és kellemesebb.
