<?php



class Expense
{
    private $expenseFile = 'expense.json';
    private $budgetFile = 'budget.json';
    private $csvFile = "Expenses.csv";

    private $current = [];
    private $budget = [];

    private $months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
    ];

    public function addExpense($options)
    {


        $id = 0;
        $month = date('F');
        $this->decode();
        $this->decodeBudget();

        try {



            // Make sure both the description and amount are added
            if (!array_key_exists('description', $options) || !array_key_exists('amount', $options)) {
                throw new Exception("Please add a --description and --amount");
            }


            if (!is_numeric($options['amount'])) {
                throw new Exception("Amount must be a number");
            }


            if ($options['amount'] < 0) {
                throw new Exception("Amount must be greater than 0");
            }

            if (empty($this->current)) {
                $id = 0;
            } else {
                $id = end($this->current)['id'] + 1;

            }


            

            // Check if it exceeds the set monthly budget
            foreach ($this->budget as $key => $value) {
                if (array_key_exists($month, $value)) {
                    if (($options['amount'] + $this->getTotalForMonth(date('n'))) > $value[$month]) {
                        echo "Warning, you have exceeded this month's budget of $" . $value[$month] . " (now " .  $this->getTotalForMonth(date('n')) + ($options['amount'] + $this->getTotalForMonth(date('n'))). ")";
                    }
                }
            }

            
            // Add to json
            $userExpense = ["id" => $id, "description" => $options["description"], "amount" => $options["amount"], "date" => date("Y/m/d")];
            $this->current[] = $userExpense;
            
            $jsonData = json_encode($this->current, JSON_PRETTY_PRINT);
            if ($jsonData === false) {
                throw new Exception("\nFailed to encode expense data to JSON");
            }
            
            $written = file_put_contents($this->expenseFile, $jsonData);
            if ($written === false) {
                throw new Exception("\nFailed to write to expense file. Check file permissions and path.");
            }
            
            return "\nExpense created successfully (ID:{$id})";
        } catch (Exception $e) {
            return $e->getMessage();
        }


    }


    public function updateExpense($options)
    {
        echo $this->decode();



        try {


            if (!array_key_exists('id', $options)) {
                throw new Exception("Please add an ID");
            }


            if (!array_key_exists('description', $options) && !array_key_exists('amount', $options)) {
                throw new Exception("Please add a --description or --amount");
            }

            $expenseToUpdateId = $options["id"];

            if (empty($this->current)) {
                return "\nNothing to update";
            } elseif (array_key_exists($expenseToUpdateId, $this->current)) {

                $expenseToUpdate = $this->current[$expenseToUpdateId];
                $expenseToUpdate["description"] = $options["description"] ?? $expenseToUpdate["description"];
                $expenseToUpdate["amount"] = $options["amount"] ?? $expenseToUpdate["amount"];

                $this->current[$this->findExpenseById($expenseToUpdateId)] = $expenseToUpdate;
                file_put_contents($this->expenseFile, json_encode($this->current, JSON_PRETTY_PRINT));

                return "Expense {$expenseToUpdateId} updated successfully";

            } else {
                return "That ID doesn't exist";
            }

        } catch (Exception $e) {
            return $e->getMessage();
        }



    }


    public function deleteExpense($options)
    {
        echo $this->decode();


        try {

            if (!array_key_exists('id', $options)) {
                throw new Exception("Please add an ID");
            }

            $expenseToDelete = $options["id"];

            if (empty($this->current)) {
                throw new Exception("\nNothing to delete");
            } elseif (array_key_exists($expenseToDelete, $this->current)) {

                unset($this->current[$options["id"]]);
                file_put_contents($this->expenseFile, json_encode($this->current, JSON_PRETTY_PRINT));
                return "Expense {$expenseToDelete} deleted successfully";
                $this->current = array_values($this->current);


            } else {
                throw new Exception("That ID does not exists");
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }


    }


    public function viewList()
    {
        $this->decode();


        // PHP code to have tabular format in command line, this was the only one i found without using a library, also it truncates values that are too big :)
        $mask = "|%5.5s |%-30.30s | %-80.80s |%-30.30s \n";

        printf($mask, 'ID', 'Date', 'Description', 'Amount');
        foreach ($this->current as $current) {

            printf($mask, $current['id'], $current['date'], $current['description'], $current['amount']);

        }

    }

    public function viewSummary($options)
    {

        $sum = 0;

        echo $this->decode();



        try {



            if (array_key_exists('month', $options)) {

                if ((!in_array($options['month'], $this->months))) {
                    throw new Exception("\nMonth isn't valid");
                }

                return "Total expense for {$options['month']}: $" . $this->getTotalForMonth($options);

            } else {

                foreach ($this->current as $current) {
                    $sum += (int)$current['amount'];
                }
                return "Total expense: $" . $sum;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }


    }


    public function setBudget($options)
    {


        $this->decodeBudget();

        $monthExists = false;
        $monthSelected = $options['month'];

        try {

            if (!array_key_exists('month', $options) || !array_key_exists('budget', $options)) {
                throw new Exception("Month or Budget not set");
            }

            if (!in_array($options['month'], $this->months)) {
                throw new Exception("Month not valid");
            }


            foreach ($this->budget as $key => $value) {

                if (array_key_exists($monthSelected, $value)) {
                    $this->budget[$key][$monthSelected] = $options['budget'];
                    $monthExists = true;
                    break;
                }
            }


            if (!$monthExists) {
                $this->budget[] = [$options['month'] => $options['budget']];
            }

            file_put_contents($this->budgetFile, json_encode($this->budget, JSON_PRETTY_PRINT));
            return "Budget successfully added";




        } catch (Exception $e) {
            return $e->getMessage();
        }




    }


    public function getTotalForMonth($options)
    {

        $sum = 0;

        foreach ($this->current as $current) {

            if (is_array($options)) {
                if (date('F', strtotime($current['date'])) == $options['month']) {
                    $sum += $current['amount'];

                }
            } else {
                if (date('m', strtotime($current['date'])) == $options) {
                    $sum += $current['amount'];

                }
            }

        }

        return $sum;
    }


    public function exportBudget()
    {
        $this->decode();

        try {
            $csv = fopen($this->csvFile, "w");


            if ($csv === false) {

                throw new Exception("File failed to open, if you have the CSV open kindly close it");
            }

            $headings = ['ID', 'Description', 'Amount', 'Date'];
            fputcsv($csv, $headings);

            foreach ($this->current as $current) {
                fputcsv($csv, $current);
            }

            return "Expenses exported to {$this->csvFile}";

            fclose($csv);


        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }


    public function help()
    {
        echo "\n=== Expense Tracker Help ===\n\n";
        echo "Usage: php expense-tracker.php -a <command> [options]\n\n";
        echo "Commands:\n";
        echo "  add              Add a new expense\n";
        echo "                   Options: --description='text' --amount=number\n\n";
        echo "  update           Update an existing expense\n";
        echo "                   Options: --id=number --description='text' --amount=number\n\n";
        echo "  delete           Delete an expense\n";
        echo "                   Options: --id=number\n\n";
        echo "  list             View all expenses in table format\n\n";
        echo "  summary          View total expenses (optionally by month)\n";
        echo "                   Options: --month=number (1-12, optional)\n\n";
        echo "  set-budget       Set or update a monthly budget\n";
        echo "                   Options: --month='MonthName' --budget=number\n\n";
        echo "  export-budget    Export all expenses to CSV file\n\n";
        echo "Examples:\n";
        echo "  php expense-tracker.php -a add --description Groceries --amount 50\n";
        echo "  php expense-tracker.php -a list\n";
        echo "  php expense-tracker.php -a summary --month May\n";
        echo "  php expense-tracker.php -a set-budget --month May --budget 500\n";
        echo "  php expense-tracker.php -a update --id 0 --description Gas --amount=40\n";
        echo "  php expense-tracker.php -a delete --id 0\n";
        echo "\n";
    }


    // Collecting the expense data from the json
    private function decode()
    {

        if (!file_exists($this->expenseFile)) {
            $this->current = [];
            return "The file doesn't exist yet, try adding an expense first";
        }


        $this->current = json_decode(file_get_contents($this->expenseFile), true);

    }


    private function decodeBudget()
    {

        if (!file_exists($this->budgetFile)) {
            $this->budget = [];
            return "The file doesn't exist yet, try adding an budget first";
        }


        $this->budget = json_decode(file_get_contents($this->budgetFile), true);

    }





    // Using array value to find index instead
    private function findExpenseById($id)
    {

        $this->decode();

        foreach ($this->current as $index => $expense) {
            if ($expense["id"] == $id) {
                return $index;
            }
        }


        return -1;
    }
}
