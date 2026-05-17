<?php

include('expenseTrackerClass.php');



$options = getopt("a:", ["id:","description:", "amount:", "month:", "budget:"]);


$a = new Expense();


switch (strToLower($options["a"])) {
    case "add":
        echo $a->addExpense($options);
        break;

    case "update":
        echo $a->updateExpense($options);
        break;

    case "delete":
        echo $a->deleteExpense($options);
        break;

    case "list":
        $a->viewList();
        break;

    case "summary":
        echo $a->viewSummary($options);
        break;

    case "set-budget":
        echo $a->setBudget($options);
        break;

    case "export-expense":
        echo $a->exportBudget();
        break;

    case "help":
        $a->help();
        break;
}
