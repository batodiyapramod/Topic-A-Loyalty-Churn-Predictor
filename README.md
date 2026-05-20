
---

# AllCalls.io Loyalty Churn Predictor (`synapcores`)

An AI-driven full-stack loyalty management dashboard and background processing engine. This system integrates with the **SynapCores AIDB** machine learning platform to predict customer churn risk profiles, rank high-value endangered accounts, and dynamically generate personalized contextual retention offers using LLM primitives.

---

## 🚀 Local Installation & Setup

Follow these sequential steps to clone, configure, and initialize the system environment locally.

### 1. Prerequisites

Ensure your local development engine matches the required ecosystem specifications:

* **PHP** 8.2 (with JSON, PDO, and cURL extensions active)
* **Composer** 2.0
* A running **SynapCores Community Edition Gateway** listening on port `8080`.

### 2. Standard Installation Steps

Execute these commands in your local shell terminal window:

```bash
# Clone the repository
git clone 
cd allcalls-churn-synapcores

# Install application dependencies
composer install

# Create environment configuration state
cp .env.example .env
php artisan key:generate

```

### 3. Environment Configuration (`.env`)

Open your newly created `.env` file and configure your local relational database connection alongside your SynapCores gateway access keys. Ensure there are no spaces or wrapping quotation marks around your generated credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=allcalls_churn
DB_USERNAME=root
DB_PASSWORD=

# SynapCores Native AI Configuration
SYNAPCORES_BASE_URL=http://127.0.0.1:8080
SYNAPCORES_TIMEOUT=5.0
SYNAPCORES_HOST=127.0.0.1
SYNAPCORES_USERNAME=admin=
SYNAPCORES_API_KEY=sc_your_actual_dashboard_generated_api_key_here

```

### 4. Database Setup & Machine Learning Pipelines

Run the initialization pipeline commands in this exact sequence to configure schemas, ingest synthesized training signals, compile the machine learning models, and generate predictive weights:

```bash
# 1. Run local schema migrations
php artisan migrate

# 2. Seed relational database and sync mirror target down to SynapCores AIDB
php artisan synapcores:seed

# 3. Compile and train the AutoML classification model inside SynapCores
php artisan synapcores:train

# 4. Trigger background prediction worker jobs synchronously to populate local cache records
php artisan tinker --execute="App\Jobs\ProcessChurnPredictionsJob::dispatchSync();"

# 5. Launch local application runtime webserver
php artisan serve

```

Navigate your browser to `[http://127.0.0.1:8000/](http://127.0.0.1:8000/dashboard)` to interact with the churn management interface.


---

## 📄 License

This take-home project is licensed under the open-source **MIT License**.
