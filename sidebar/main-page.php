<?php

session_start();
$conn = require '../database.php';

if (isset($_SESSION["customer_id"])) {
    
    $customer_id = $_SESSION['customer_id'];

// Fetch profile
$stmt = $conn->prepare("SELECT name, profile_image FROM customer WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['customer_id']);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

// Fetch total expenses
$total_expense = 0;
$stmt = $conn->prepare("SELECT SUM(amount) AS total FROM expense WHERE customer_id = ?");
$stmt->bind_param("i", $_SESSION['customer_id']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if ($row && $row['total']) {
    $total_expense = $row['total'];
}

// Fetch recent expenses (limit 10)
$expenses = [];
  $stmt = $conn->prepare("SELECT expense_name, amount, category, date 
                          FROM expense 
                          WHERE customer_id = ? 
                          ORDER BY date DESC 
                          LIMIT 10");
$stmt->bind_param("i", $_SESSION['customer_id']);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $expenses[] = $row;
}

}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SmartSpend</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
    integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK" crossorigin="anonymous">
    </script>
  <link rel="stylesheet" href="styledash.css">
  <style>
      body {
        background-color: #f8f9fa;
      }
      
      .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
      }
      
      .card:hover {
        transform: translateY(-5px);
      }
      
      .balance-card {
        background: linear-gradient(135deg, #4c8444 0%, #102820 100%);
        color: white;
      }
      
      .stat-card {
        background: white;
      }
      
      .currency-card {
        background: linear-gradient(135deg, #C7AD7F 0%, #AE6E4E 100%);
        color: white;
      }
      
      .payment-card {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
      }
      
      .announcement-card {
        background: linear-gradient(135deg, #ba6240 0%, #4d2018 100%);
        color: white;
      }
      
      .chart-placeholder {
        height: 200px;
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
      }

      .loading-spinner {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
      }

      @keyframes spin {
        to { transform: rotate(360deg); }
      }

      .rate-up {
        color: #28a745;
      }

      .rate-down {
        color: #dc3545;
      }

      .last-updated {
        font-size: 0.75rem;
        opacity: 0.8;
        margin-top: 10px;
      }

      .expenses-container {
        max-height: 300px;
        overflow-y: auto;
        margin-top: 10px;
      }

      .expense-item {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 8px;
        border-left: 4px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
      }

      .expense-item:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateX(5px);
      }

      .expense-item:last-child {
        margin-bottom: 0;
      }

      .expense-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 4px;
      }

      .expense-category {
        font-size: 0.8rem;
        opacity: 0.8;
        background: rgba(255, 255, 255, 0.2);
        padding: 2px 8px;
        border-radius: 12px;
        display: inline-block;
      }

      .expense-amount {
        font-weight: bold;
        font-size: 1.1rem;
      }

      .expense-date {
      font-size: 0.75rem;
      opacity: 0.7;
    }

    .expenses-container::-webkit-scrollbar {
      width: 6px;
    }

    .expenses-container::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 3px;
    }

    .expenses-container::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.3);
      border-radius: 3px;
    }

    .expenses-container::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.5);
    }
  </style>
</head>



<body>
  <div class="container-fluid">
    <div class="row vh-100">
      
      <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary shadow-lg sticky-top" style="width: 280px;">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">

          <span class="fs-4">SmartSpend</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
          <li class="nav-item">
            <a href="main-page.php" class="nav-link active m-1" aria-current="page">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-clipboard-data-fill" viewBox="0 0 16 16">
                <path
                  d="M6.5 0A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0zm3 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5z" />
                <path
                  d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1A2.5 2.5 0 0 1 9.5 5h-3A2.5 2.5 0 0 1 4 2.5zM10 8a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0zm-6 4a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0zm4-3a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1" />
              </svg>
              </svg>
              Dashboard
            </a>
          </li>

          <li>
            <a href="expenses.php" class="nav-link link-body-emphasis m-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-credit-card-fill" viewBox="0 0 16 16">
                <path
                  d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1H0zm0 3v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7zm3 2h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1" />
              </svg>
              Expense Tracker
            </a>
          </li>
          <li>
            <a href="cur-converter.php" class="nav-link link-body-emphasis m-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-currency-exchange" viewBox="0 0 16 16">
                <path
                  d="M0 5a5 5 0 0 0 4.027 4.905 6.5 6.5 0 0 1 .544-2.073C3.695 7.536 3.132 6.864 3 5.91h-.5v-.426h.466V5.05q-.001-.07.004-.135H2.5v-.427h.511C3.236 3.24 4.213 2.5 5.681 2.5c.316 0 .59.031.819.085v.733a3.5 3.5 0 0 0-.815-.082c-.919 0-1.538.466-1.734 1.252h1.917v.427h-1.98q-.004.07-.003.147v.422h1.983v.427H3.93c.118.602.468 1.03 1.005 1.229a6.5 6.5 0 0 1 4.97-3.113A5.002 5.002 0 0 0 0 5m16 5.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0m-7.75 1.322c.069.835.746 1.485 1.964 1.562V14h.54v-.62c1.259-.086 1.996-.74 1.996-1.69 0-.865-.563-1.31-1.57-1.54l-.426-.1V8.374c.54.06.884.347.966.745h.948c-.07-.804-.779-1.433-1.914-1.502V7h-.54v.629c-1.076.103-1.808.732-1.808 1.622 0 .787.544 1.288 1.45 1.493l.358.085v1.78c-.554-.08-.92-.376-1.003-.787zm1.96-1.895c-.532-.12-.82-.364-.82-.732 0-.41.311-.719.824-.809v1.54h-.005zm.622 1.044c.645.145.943.38.943.796 0 .474-.37.8-1.02.86v-1.674z" />
              </svg>
              Currency Converter
            </a>
          </li>
        </ul>
        <hr>
        <div class="dropdown">
          <a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle"
            data-bs-toggle="dropdown" aria-expanded="false">
            <img src="../uploads/profile_images/<?= htmlspecialchars($customer['profile_image']) ?>" 
              alt="Profile" width="32" height="32" class="rounded-circle me-2">
            <strong><?= htmlspecialchars($customer['name']) ?></strong>
          </a>
          <ul class="dropdown-menu text-small shadow">
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="../logout.php">Sign out</a></li>
          </ul>
        </div>
      </div>

      <div class="col-sm-9  p-4">
        <!-- Header -->
        <div class="mb-4">
          <h2 class="fw-bold mb-1">Welcome back, <?= htmlspecialchars($customer['name']) ?>!</h2>
          <p class="text-muted mb-0">Here's what's happening with your finances today.</p>
        </div>

          <!-- Current Rates -->
          <div class="col-md-6 col-lg-6">
            <div class="card currency-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="card-subtitle mb-0 opacity-75">Live Exchange Rates</h6>
                  <button class="btn btn-sm btn-outline-light opacity-75" onclick="refreshRates()" id="refreshBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                      class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z" />
                      <path
                        d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z" />
                    </svg>
                  </button>
                </div>
                <div class="exchange-rates">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>USD/MYR</span>
                    <div class="text-end">
                      <strong id="usd-rate">
                        <span class="loading-spinner"></span>
                      </strong>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>EUR/MYR</span>
                    <div class="text-end">
                      <strong id="eur-rate">
                        <span class="loading-spinner"></span>
                      </strong>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>GBP/MYR</span>
                    <div class="text-end">
                      <strong id="gbp-rate">
                        <span class="loading-spinner"></span>
                      </strong>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <span>JPY/MYR</span>
                    <div class="text-end">
                      <strong id="jpy-rate">
                        <span class="loading-spinner"></span>
                      </strong>
                    </div>
                  </div>
                </div>
                <div class="last-updated text-center" id="last-updated">
                  Loading rates...
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Second Row -->
        <div class="row g-4">
          <!-- Recent Expenses -->
          <div class="col-md-12">
            <div class="card announcement-card">
              <div class="card-body py-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                      class="bi bi-receipt me-3" viewBox="0 0 16 16">
                      <path
                        d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zM2 2.5v11h12v-11H2zm1 2h10v1H3v-1zm0 2h10v1H3v-1zm0 2h10v1H3v-1zm0 2h10v1H3v-1z" />
                    </svg>
                    <h5 class="mb-0 fw-bold">Recent Expenses</h5>
                  </div>
                  <button class="btn btn-sm btn-outline-light opacity-75" onclick="addSampleExpense()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                      class="bi bi-plus-circle" viewBox="0 0 16 16">
                      <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                      <path
                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                    </svg>
                  </button>
                </div>
                <div class="expenses-container" id="expenses-container">
                  <div class="text-center opacity-75 py-3" id="no-expenses">
                    No recent expenses. Add your first expense to get started!
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
    let previousRates = {};
    let expenses = <?= json_encode($expenses) ?>;

    const expenseCategories = {
      'Food': '#28a745',
      'Transportation': '#007bff',
      'Shopping': '#ffc107',
      'Entertainment': '#e83e8c',
      'Bills': '#6c757d',
      'Healthcare': '#20c997',
      'Education': '#6f42c1',
      'Travel': '#fd7e14',
      'Groceries': '#198754',
      'Others': '#495057'
    };

    function formatDate(date) {
      const now = new Date();
      const diffTime = Math.abs(now - date);
      const diffHours = Math.floor(diffTime / (1000 * 60 * 60));
      
      if (diffHours < 1) {
        const diffMinutes = Math.floor(diffTime / (1000 * 60));
        return `${diffMinutes} minutes ago`;
      } else if (diffHours < 24) {
        return `${diffHours} hours ago`;
      } else {
        const diffDays = Math.floor(diffHours / 24);
        return `${diffDays} days ago`;
      }
    }

    function renderExpenses() {
      const container = document.getElementById('expenses-container');
      const noExpenses = document.getElementById('no-expenses');

      if (!expenses || expenses.length === 0) {
        noExpenses.style.display = 'block';
        return;
      }

      noExpenses.style.display = 'none';

      const sortedExpenses = [...expenses].sort((a, b) => new Date(b.date) - new Date(a.date));

      container.innerHTML = sortedExpenses.map(expense => `
        <div class="expense-item">
          <div class="expense-header">
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="fw-bold">${expense.expense_name}</div>
                  <div class="expense-date">${formatDate(new Date(expense.date))}</div>
                </div>
                <div class="text-end">
                  <div class="expense-amount">RM ${parseFloat(expense.amount).toFixed(2)}</div>
                  <div class="expense-category">${expense.category}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      `).join('');
    }

    async function fetchExchangeRates() {
      try {
        const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
        const data = await response.json();
        const myrRate = data.rates.MYR;

        return {
          USD: myrRate,
          EUR: data.rates.EUR * myrRate,
          GBP: data.rates.GBP * myrRate,
          JPY: (data.rates.JPY * myrRate) / 100
        };
      } catch (error) {
        console.error('Error fetching exchange rates:', error);
        return {
          USD: 4.29,
          EUR: 4.68,
          GBP: 5.35,
          JPY: 2.87
        };
      }
    }

    function getChangeClass(current, previous) {
      if (!previous) return '';
      if (current > previous) return 'rate-up';
      if (current < previous) return 'rate-down';
      return '';
    }

    function getChangeSymbol(current, previous) {
      if (!previous) return '';
      if (current > previous) return ' ↗';
      if (current < previous) return ' ↘';
      return '';
    }

    function updateRateDisplay(elementId, rate, currency) {
      const element = document.getElementById(elementId);
      const changeClass = getChangeClass(rate, previousRates[currency]);
      const changeSymbol = getChangeSymbol(rate, previousRates[currency]);

      const formattedRate = currency === 'JPY' ? 
        `${rate.toFixed(2)}/100` : 
        rate.toFixed(4);

      element.innerHTML = `<span class="${changeClass}">${formattedRate}${changeSymbol}</span>`;
      previousRates[currency] = rate;
    }

    async function refreshRates() {
      const refreshBtn = document.getElementById('refreshBtn');
      refreshBtn.style.opacity = '0.5';
      refreshBtn.disabled = true;

      try {
        const rates = await fetchExchangeRates();

        updateRateDisplay('usd-rate', rates.USD, 'USD');
        updateRateDisplay('eur-rate', rates.EUR, 'EUR');
        updateRateDisplay('gbp-rate', rates.GBP, 'GBP');
        updateRateDisplay('jpy-rate', rates.JPY, 'JPY');

        const now = new Date();
        const timeString = now.toLocaleTimeString('en-MY', { 
          hour: '2-digit', 
          minute: '2-digit',
          timeZone: 'Asia/Kuala_Lumpur'
        });
        document.getElementById('last-updated').textContent = `Last updated: ${timeString} MYT`;
        
      } catch (error) {
        console.error('Error refreshing rates:', error);
        document.getElementById('last-updated').textContent = 'Error updating rates';
      } finally {
        refreshBtn.style.opacity = '1';
        refreshBtn.disabled = false;
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      renderExpenses();
      refreshRates();
      setInterval(refreshRates, 300000); // Refresh every 5 minutes
    });
</script>


</body>

</html>