It sounds like you have hit the classic **"Trait Soup"** wall. When traits are overused, they turn into a disorganized pile of methods where state is completely hidden, making it a nightmare to know where variables (like your tenant or database mode) are coming from.

Let's untangle this. Here is how you can move away from traits and clean up your architecture.

---

## 1. Should You Use Classes or Static Classes?

**Avoid Static Classes for Core Logic.**
Static classes make it impossible to use dependency injection, they couple your code tightly, and they are incredibly hard to test.

**Use Standard Classes with Context Injection.**
Instead of forcing every function to ask, *"Who is the tenant, and what database am I using?"*, you should let the **class instance** hold that state.

```php
// ❌ THE MESSY WAY (Pass everything everywhere, or use trait hidden state)
$prospects = ProspectQueries::getProspects($tenantId, 'dynamic');

//  THE CLEAN WAY (Stateful Class via Context)
$tenantContext = new TenantContext($tenantId, 'dynamic');
$prospectRepository = new ProspectRepository($tenantContext);

// Now the method doesn't care about tenant details; the class handles it internally!
$prospects = $prospectRepository->getAll();

```

---

## 2. Solving the "Passing Too Much Data Around" Problem

Right now, you are treating your code like a procedural script wrapped in traits. To fix the constant passing of `tenantId` and `databaseMode`, you need a **`TenantContext`** object.

### Step 1: Create a Context Object

Create a simple class that encapsulates the environment configuration for the current request.

```php
class TenantContext {
    private string $tenantId;
    private string $dbMode; // 'global' or 'dynamic'

    public function __construct(string $tenantId, string $dbMode = 'dynamic') {
        $this->tenantId = $tenantId;
        $this->dbMode = $dbMode;
    }

    public function getTenantId(): string { return $this->tenantId; }
    public function getDbMode(): string { return $this->dbMode; }
}

```

### Step 2: Inject it into Repositories / Services

Instead of splitting your queries into database traits (`DatabaseQuery`, `ProspectQueries`, `AccumulatorQueries`), group them into **Repositories** that consume your `TenantContext`.

```php
class ProspectRepository {
    private TenantContext $context;
    private PDO $db;

    public function __construct(TenantContext $context, DatabaseConnectionManager $dbManager) {
        $this->context = $context;
        // The manager decides which DB connection to return based on the context!
        $this->db = $dbManager->getConnection($context->getDbMode()); 
    }

    public function findById(int $id) {
        // Automatically filtered by the tenant bound to this repository instance
        $stmt = $this->db->prepare("SELECT * FROM prospects WHERE id = :id AND tenant_id = :tenant");
        $stmt->execute(['id' => $id, 'tenant' => $this->context->getTenantId()]);
        return $stmt->fetch();
    }
}

```

---

## 3. Calling PHP Functions instead of Postman/URL Requests

If you are making internal HTTP requests (e.g., `ContactController` hitting a URL on `AdminController` via Curl/Guzzle), you are adding unnecessary network latency and serialization overhead.

You need to extract the logic out of your Controllers and into **Service Classes**.

* **Controllers** should *only* handle HTTP requests, validate input, and return responses.
* **Services** should contain the actual business logic.

### Example Transformation

**Before (HTTP-based execution):**

```php
// Inside ContactController...
$response = $httpClient->post('/api/admin/migrate', ['tenant_id' => 5]); 

```

**After (PHP Native Service execution):**

```php
class ContactController {
    private MigrationService $migrationService;

    // Inject the service directly
    public function __construct(MigrationService $migrationService) {
        $this->migrationService = $migrationService;
    }

    public function handleContactRequest($request) {
        // A clean, native PHP function call! No URLs required.
        $this->migrationService->runMigrationForTenant($request->tenantId);
    }
}

```

---

## 🛠️ Step-by-Step Clean Up Plan for Your Directory

Looking at your codebase structure (`backend/packages/traits/...`), here is exactly how to migrate it:

1. **`traits/convert/Base62Convert.php` & `Utils.php**`:
These are actually perfect candidates for pure, stateless helper classes. Turn them into standard utility classes (`class Base62Converter { public function encode(...) }`).
2. **`traits/database/` (`DatabaseQuery`, `DatabaseInsert`, etc.)**:
Delete these traits. Replace them with a robust `DatabaseConnectionManager` class that reads from your `TenantContext` to switch between `global` and `dynamic` databases seamlessly.
3. **`traits/prospect/ProspectQueries.php` & `accumulator/AccumulatorQueries.php**`:
Convert these into `ProspectRepository` and `AccumulatorRepository` classes. Pass the `TenantContext` into their constructors so you never have to pass `$tenantId` as a function argument ever again.

By introducing a central state manager (Context) and utilizing Dependency Injection, your code will instantly become readable, testable, and completely clear of trait clutter.