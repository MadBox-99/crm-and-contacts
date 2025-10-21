# CRM Rendszer Fejlesztési Terv

## Projekt áttekintés

Laravel 12 alapú CRM rendszer fejlesztése, amely megfelel a GINOP PLUSZ-1.2.4-24 felhívás követelményeinek.

### Használt technológiák
- Laravel 12
- Filament v4 (Admin panel és UI komponensek)
- Livewire v3
- Pest v4 (tesztelés)
- Tailwind CSS v4
- PHP 8.4

## Adatbázis struktúra

### Főbb modellek és kapcsolatok

#### 0. Alapvető rendszer táblák
```
- User (Felhasználó)
  - id
  - name
  - email (unique)
  - email_verified_at (timestamp, nullable)
  - password
  - remember_token
  - timestamps

- password_reset_tokens
  - email (primary)
  - token
  - created_at (timestamp, nullable)

- sessions
  - id (primary)
  - user_id (foreign key, nullable, indexed)
  - ip_address (varchar 45, nullable)
  - user_agent (text, nullable)
  - payload (long text)
  - last_activity (integer, indexed)

- personal_access_tokens (Laravel Sanctum)
- permissions és roles táblák (Spatie Permission csomag)
- cache, cache_locks táblák (Laravel cache)
- jobs, job_batches, failed_jobs táblák (Laravel queue)
```

#### 1. Ügyfélkezelés (Customers)
```
- Customer (Ügyfél)
  - id
  - unique_identifier (egyedi azonosító, unique)
  - name
  - type (B2B/B2C, default: B2C)
  - tax_number (nullable)
  - registration_number (nullable)
  - email (nullable)
  - phone (nullable)
  - notes (text, nullable)
  - is_active (boolean, default: true)
  - timestamps
  - soft_deletes

- CustomerContact (Kapcsolattartó)
  - id
  - customer_id (foreign key -> customers, cascade on delete)
  - name
  - email (nullable)
  - phone (nullable)
  - position (nullable)
  - is_primary (boolean, default: false)
  - timestamps

- CustomerAddress (Cím)
  - id
  - customer_id (foreign key -> customers, cascade on delete)
  - type (default: 'billing')
  - country
  - postal_code
  - city
  - street
  - building_number (nullable)
  - floor (nullable)
  - door (nullable)
  - is_default (boolean, default: false)
  - timestamps

- CustomerAttribute (Egyedi jellemzők)
  - id
  - customer_id (foreign key -> customers, cascade on delete)
  - attribute_key
  - attribute_value (text, nullable)
  - timestamps
```

#### 2. Értékesítés (Sales)
```
- Campaign (Kampány)
  - id
  - name
  - description (text, nullable)
  - start_date (date)
  - end_date (date, nullable)
  - status (default: 'draft' via CampaignStatus enum)
  - target_audience_criteria (JSON, nullable)
  - created_by (foreign key -> users, nullable, null on delete)
  - timestamps
  - soft_deletes

- CampaignResponse (Kampány válasz)
  - id
  - campaign_id (foreign key -> campaigns, cascade on delete)
  - customer_id (foreign key -> customers, cascade on delete)
  - response_type (enum: interested/not_interested/callback/no_response, default: no_response)
  - notes (text, nullable)
  - responded_at (timestamp, nullable)
  - timestamps

- Opportunity (Értékesítési lehetőség)
  - id
  - customer_id (foreign key -> customers, cascade on delete)
  - title
  - description (text, nullable)
  - value (decimal 12,2, nullable)
  - probability (integer, default: 0)
  - stage (enum via OpportunityStage, default: 'lead')
  - expected_close_date (date, nullable)
  - assigned_to (foreign key -> users, nullable, null on delete)
  - timestamps
  - soft_deletes

- Quote (Ajánlat)
  - id
  - customer_id (foreign key -> customers, cascade on delete)
  - opportunity_id (foreign key -> opportunities, nullable, null on delete)
  - quote_number (unique)
  - issue_date (date)
  - valid_until (date)
  - status (enum: draft/sent/accepted/rejected/expired, default: draft)
  - subtotal (decimal 12,2, default: 0)
  - discount_amount (decimal 12,2, default: 0)
  - tax_amount (decimal 12,2, default: 0)
  - total (decimal 12,2, default: 0)
  - notes (text, nullable)
  - timestamps
  - soft_deletes

- QuoteItem (Ajánlat tétel)
  - id
  - quote_id (foreign key -> quotes, cascade on delete)
  - product_id (foreign key -> products, nullable, null on delete)
  - description
  - quantity (decimal 10,2, default: 1)
  - unit_price (decimal 12,2, default: 0)
  - discount_percent (decimal 5,2, default: 0)
  - discount_amount (decimal 12,2, default: 0)
  - tax_rate (decimal 5,2, default: 0)
  - total (decimal 12,2, default: 0)
  - timestamps
```

#### 3. Rendelés és számlázás
```
- Order (Rendelés)
  - id
  - customer_id (foreign key -> customers, cascade on delete)
  - quote_id (foreign key -> quotes, nullable, null on delete)
  - order_number (unique)
  - order_date (date)
  - status (enum via OrderStatus: Pending/Confirmed/Processing/Shipped/Delivered/Cancelled, default: Pending)
  - subtotal (decimal 12,2, default: 0)
  - discount_amount (decimal 12,2, default: 0)
  - tax_amount (decimal 12,2, default: 0)
  - total (decimal 12,2, default: 0)
  - notes (text, nullable)
  - timestamps
  - soft_deletes

- OrderItem (Rendelés tétel)
  - id
  - order_id (foreign key -> orders, cascade on delete)
  - product_id (foreign key -> products, nullable, null on delete)
  - description
  - quantity (integer, default: 1)
  - unit_price (decimal 12,2, default: 0)
  - discount_amount (decimal 12,2, default: 0)
  - tax_rate (decimal 5,2, default: 0)
  - total (decimal 12,2, default: 0)
  - timestamps

- Invoice (Számla)
  - id
  - customer_id (foreign key -> customers, cascade on delete)
  - order_id (foreign key -> orders, nullable, null on delete)
  - invoice_number (unique)
  - issue_date (date)
  - due_date (date)
  - status (default: 'draft')
  - subtotal (decimal 12,2, default: 0)
  - discount_amount (decimal 12,2, default: 0)
  - tax_amount (decimal 12,2, default: 0)
  - total (decimal 12,2, default: 0)
  - notes (text, nullable)
  - paid_at (timestamp, nullable)
  - timestamps
  - soft_deletes
```

#### 4. Kedvezmények
```
- Discount (Kedvezmény)
  - id
  - name
  - type (enum via DiscountType: Quantity/ValueThreshold/TimeBased/Custom, default: Custom)
  - value_type (enum via DiscountValueType: Percentage/Fixed, default: Percentage)
  - value (decimal 12,2, default: 0)
  - min_quantity (decimal 10,2, nullable)
  - min_value (decimal 12,2, nullable)
  - valid_from (date, nullable)
  - valid_until (date, nullable)
  - customer_id (foreign key -> customers, nullable, cascade on delete)
  - product_id (foreign key -> products, nullable, cascade on delete)
  - is_active (boolean, default: true)
  - description (text, nullable)
  - timestamps
  - soft_deletes
```

#### 5. Ügyfélkapcsolat kezelés
```
- Interaction (Kapcsolatfelvétel)
  - id
  - customer_id (foreign key -> customers, cascade on delete)
  - user_id (foreign key -> users, cascade on delete)
  - type (enum: call/email/meeting/note, default: note)
  - subject
  - description (text, nullable)
  - interaction_date (timestamp)
  - duration (integer, nullable) - Duration in minutes
  - next_action (nullable)
  - next_action_date (date, nullable)
  - timestamps

- Task (Feladat)
  - id
  - customer_id (foreign key -> customers, nullable, cascade on delete)
  - assigned_to (foreign key -> users, cascade on delete)
  - assigned_by (foreign key -> users, cascade on delete)
  - title
  - description (text, nullable)
  - priority (enum: low/medium/high/urgent, default: medium)
  - status (enum: pending/in_progress/completed/cancelled, default: pending)
  - due_date (date, nullable)
  - completed_at (timestamp, nullable)
  - timestamps

- Complaint (Reklamáció)
  - id
  - customer_id (foreign key -> customers, cascade on delete)
  - order_id (foreign key -> orders, nullable, null on delete)
  - reported_by (foreign key -> users, cascade on delete)
  - assigned_to (foreign key -> users, nullable, null on delete)
  - title
  - description (text)
  - severity (enum: low/medium/high/critical, default: medium)
  - status (enum: open/in_progress/resolved/closed, default: open)
  - resolution (text, nullable)
  - reported_at (timestamp)
  - resolved_at (timestamp, nullable)
  - timestamps

- ComplaintEscalation (Eszkaláció)
  - id
  - complaint_id (foreign key -> complaints, cascade on delete)
  - escalated_to (foreign key -> users, cascade on delete)
  - escalated_by (foreign key -> users, cascade on delete)
  - reason (text)
  - escalated_at (timestamp)
  - timestamps
```

#### 6. Kommunikáció
```
- Communication (Kommunikáció)
  - id
  - customer_id (foreign key -> customers, nullable, cascade on delete)
  - channel (enum via CommunicationChannel: Email/Sms/Chat/Social, default: Email)
  - direction (enum via CommunicationDirection: Inbound/Outbound, default: Outbound)
  - subject (nullable)
  - content (text)
  - status (enum via CommunicationStatus: Pending/Sent/Delivered/Failed/Read, default: Pending)
  - sent_at (timestamp, nullable)
  - delivered_at (timestamp, nullable)
  - read_at (timestamp, nullable)
  - timestamps

- ChatSession (Chat munkamenet)
  - id
  - customer_id (foreign key -> customers, nullable, cascade on delete)
  - user_id (foreign key -> users, nullable, null on delete)
  - started_at (timestamp)
  - ended_at (timestamp, nullable)
  - status (enum: active/closed/transferred, default: active)
  - timestamps

- ChatMessage (Chat üzenet)
  - id
  - chat_session_id (foreign key -> chat_sessions, cascade on delete)
  - sender_type (enum: customer/user/bot, default: customer)
  - sender_id (unsigned big integer, nullable)
  - message (text)
  - timestamps
```

#### 7. Termékek
```
- Product (Termék)
  - id
  - name
  - sku (unique)
  - description (text, nullable)
  - category_id (foreign key -> product_categories, nullable, null on delete)
  - unit_price (decimal 12,2, default: 0)
  - tax_rate (decimal 5,2, default: 0)
  - is_active (boolean, default: true)
  - timestamps
  - soft_deletes

- ProductCategory (Termékkategória)
  - id
  - name
  - parent_id (foreign key -> product_categories, nullable, null on delete)
  - description (text, nullable)
  - timestamps
```

#### 8. Rendszer funkciók
```
- AuditLog (Napló)
  - id
  - user_id (foreign key -> users, nullable, null on delete)
  - model_type
  - model_id (unsigned big integer)
  - action
  - old_values (JSON, nullable)
  - new_values (JSON, nullable)
  - ip_address (varchar 45, nullable)
  - user_agent (text, nullable)
  - timestamps
  - index: [model_type, model_id]

- BugReport (Hibabejelentés)
  - id
  - user_id (foreign key -> users, nullable, null on delete)
  - title
  - description (text)
  - severity (enum: low/medium/high/critical, default: medium)
  - status (enum: open/in_progress/resolved/closed/rejected, default: open)
  - assigned_to (foreign key -> users, nullable, null on delete)
  - resolved_at (timestamp, nullable)
  - timestamps

- Notification (Értesítés)
  - id
  - user_id
  - type
  - title
  - message
  - data (JSON)
  - read_at
  - timestamps

  Megjegyzés: A Notification tábla nincs még migration-ben definiálva.
```

## Widgets és Dashboard

```
app/Filament/Widgets/
├── SalesStatsWidget (Értékesítési statisztikák)
├── CustomerActivityWidget (Ügyfél aktivitás)
├── CampaignPerformanceWidget (Kampány teljesítmény)
├── RevenueChartWidget (Bevétel grafikon)
├── TopCustomersWidget (Top ügyfelek)
├── UpcomingTasksWidget (Közelgő feladatok)
└── ComplaintStatusWidget (Reklamációk státusza)
```

## API Endpoints (app/Http/Controllers/Api/)

```
/api/v1/customers
├── GET    /              (lista)
├── POST   /              (létrehozás)
├── GET    /{id}          (részletek)
├── PUT    /{id}          (módosítás)
└── DELETE /{id}          (törlés)

/api/v1/campaigns
/api/v1/opportunities
/api/v1/quotes
/api/v1/orders
/api/v1/invoices
/api/v1/complaints
/api/v1/products
/api/v1/communications
```

## Feladatok ütemezése (Laravel Scheduler)

```
app/Console/Commands/
├── SendCampaignEmailsCommand
├── CheckOverdueInvoicesCommand
├── GenerateSalesReportsCommand
├── CleanupOldLogsCommand
└── BackupDatabaseCommand
```

## Tesztelési stratégia

### Feature tesztek (tests/Feature/)
```
- CustomerManagementTest
- CampaignManagementTest
- OpportunityManagementTest
- QuoteGenerationTest
- OrderProcessingTest
- ComplaintHandlingTest
- DiscountApplicationTest
- ReportGenerationTest
- ApiAuthenticationTest
```

### Unit tesztek (tests/Unit/)
```
- DiscountCalculationTest
- QuotePricingTest
- CustomerValidationTest
- CampaignAudienceFilterTest
```

## Biztonsági funkciók

### Autentikáció és Authorizáció
- Laravel Sanctum API tokenek
- Filament jogosultság-kezelés
- Role-based access control (RBAC)
- Kétfaktoros authentikáció (2FA)

### Policies (app/Policies/)
```
- CustomerPolicy
- CampaignPolicy
- OpportunityPolicy
- QuotePolicy
- OrderPolicy
- ComplaintPolicy
```

## Integrációk

### Email integráció
- Laravel Mail + Mailables
- Email templates (resources/views/emails/)
- Queue-ba helyezett email küldés

### Exportálás
- Excel export (Laravel Excel)
- PDF generálás (DomPDF/Snappy)
- CSV export

### Külső API integrációk
- Social Media API-k (Facebook, Twitter)
- SMS gateway integráció
- Accounting software integration (API-kon keresztül)

## Telepítési és üzemeltetési követelmények

### Környezet
- PHP 8.4+
- MySQL 8.0+ / PostgreSQL 14+
- Redis (cache és queue)
- Node.js 18+ (frontend build)

### Biztonsági mentés
- Napi automatikus adatbázis backup
- File storage backup
- Disaster Recovery Plan dokumentáció

### Monitoring és logging
- Laravel Log
- Application Performance Monitoring
- Error tracking (pl. Sentry)

### Dokumentáció
- Felhasználói kézikönyv (magyar)
- Adminisztrátori kézikönyv (magyar)
- API dokumentáció (OpenAPI/Swagger)
- Deployment útmutató
- Verziókezelési stratégia

## Fejlesztési fázisok

### 1. Fázis - Alapok (1-2 hét)
- Projekt inicializálás
- Adatbázis migráció és seeders
- User management és autentikáció
- Alapvető Filament panel beállítás

### 2. Fázis - Ügyfélkezelés (2-3 hét)
- Customer CRUD
- Contact és Address kezelés
- Customer attributes
- Audit logging

### 3. Fázis - Értékesítés (3-4 hét)
- Campaign management
- Opportunity tracking
- Quote generation
- Order processing
- Invoice generation
- Discount system

### 4. Fázis - Kommunikáció (2-3 hét)
- Interaction logging
- Task management
- Email integration
- SMS integration
- Chat system

### 5. Fázis - Ügyfélszolgálat (2 hét)
- Complaint management
- Escalation workflow
- Customer feedback

### 6. Fázis - Jelentések és analitika (2 hét)
- Dashboard widgets
- Custom reports
- Data visualization
- Export funkcionalitás

### 7. Fázis - API és Integrációk (2 hét)
- REST API
- External integrations
- Webhook support

### 8. Fázis - Tesztelés és dokumentáció (2-3 hét)
- Feature és unit tesztek
- Felhasználói dokumentáció
- Adminisztrátori dokumentáció
- API dokumentáció

### 9. Fázis - Élesítés (1 hét)
- Production deployment
- Adatmigráció
- Felhasználói képzés
- Rendszer átadás

## Következő lépések

1. Adatbázis migrációk elkészítése
2. Model osztályok létrehozása factory-kkal és seeder-ekkel
3. Filament Resources kialakítása
4. Policy-k és permission rendszer implementálása
5. Alapvető tesztek írása
6. API endpoints kialakítása
7. Dokumentáció készítése
