# 1. Entity: fund_accounts
### Front-End Documentation
Features:
Display a list of all fund accounts with their names, balances, and fees.
Allow the creation, update, and deletion of fund accounts.
Display detailed information for a selected fund account.
UI Fields:
Name (text input).
Balance (read-only numeric display).
Fees (read-only numeric display).
Total Balance (calculated display).

### Business Documentation
fund_accounts:
Represents the core financial entities in the loan fund system.
Tracks financial metrics for fund management, including the collection of fees and total balances.
Ensures clear separation of financial entities for accountability.


# 2. Entity: assets
### Front-End Documentation
Features:
Display assets for each fund account.
Allow creation, update, and deletion of assets.
Provide detailed views of each asset, including title, cost, and description.
UI Fields:
Title (text input).
Cost (numeric input with currency format).
Description (optional text input).

#### Business Documentation
assets:
Represents tangible or intangible resources owned by a fund account.
Provides a granular view of a fund’s holdings, ensuring proper tracking and reporting.
