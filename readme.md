# Expense Tracker

A simple command-line expense tracking application built with PHP. Manage your expenses and budgets efficiently from the terminal.

## Features

- **Add Expenses**: Track daily expenses with descriptions and amounts
- **Update Expenses**: Modify existing expense records
- **Delete Expenses**: Remove expenses from your tracker
- **View Expenses**: Display all expenses in an organized table format
- **Monthly Summary**: View total expenses for a specific month or all expenses
- **Budget Management**: Set and update monthly budgets
- **Export to CSV**: Export all expenses to a CSV file for further analysis

## Requirements

- PHP 7.0 or higher
- Command-line access

## Installation

1. Clone or download this repository to your local machine
2. Navigate to the project directory:
   ```bash
   cd "Expense Tracker"
   ```

## Usage

### Basic Syntax
```bash
php expense-tracker.php -a <command> [options]
```

### Commands

#### Add an Expense
```bash
php expense-tracker.php -a add --description='Groceries' --amount=50
```
- `--description`: Description of the expense (required)
- `--amount`: Amount spent (required)
- Date is automatically set to today's date

#### Update an Expense
```bash
php expense-tracker.php -a update --id=0 --description='Gas' --amount=40
```
- `--id`: The ID of the expense to update (required)
- `--description`: New description (required)
- `--amount`: New amount (required)

#### Delete an Expense
```bash
php expense-tracker.php -a delete --id=0
```
- `--id`: The ID of the expense to delete (required)

#### View All Expenses
```bash
php expense-tracker.php -a list
```
Displays all expenses in a formatted table with columns for ID, Date, Description, and Amount.

#### View Summary
```bash
# Total expenses for all time
php expense-tracker.php -a summary

# Total expenses for a specific month
php expense-tracker.php -a summary --month=5
```
- `--month`: Month number (1-12, optional)

#### Set a Budget
```bash
php expense-tracker.php -a set-budget --month='May' --budget=500
```
- `--month`: Full month name (January-December, required)
- `--budget`: Budget amount (required)

#### Export to CSV
```bash
php expense-tracker.php -a export-budget
```
Exports all expenses to `Expenses.csv` in the current directory.

#### Get Help
```bash
php expense-tracker.php -h add
```
Displays help information for all available commands.

## Data Storage

- **Expenses**: Stored in `expense.json`
- **Budgets**: Stored in `budget.json`

Both files are automatically created when you first use the application.

## File Structure

```
Expense Tracker/
├── expense-tracker.php      # Main entry point
├── expenseTrackerClass.php  # Expense class with all functionality
├── expense.json             # Expense data (auto-generated)
├── budget.json              # Budget data (auto-generated)
├── Expenses.csv             # Exported expenses (generated on export)
└── readme.md                # This file
```

## Example Workflow

1. Add an expense:
   ```bash
   php expense-tracker.php -a add --description Coffee --amount 5
   ```

2. View your expenses:
   ```bash
   php expense-tracker.php -a list
   ```

3. Set a monthly budget:
   ```bash
   php expense-tracker.php -a set-budget --monthM May --budget 500
   ```

4. Check your spending:
   ```bash
   php expense-tracker.php -a summary --month May
   ```

5. Export for analysis:
   ```bash
   php expense-tracker.php -a export-budget
   ```

## License

This project is open source and available for personal use.
