<?php

namespace App\Models;

use App\Models\File;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Program extends Model
{
    use HasFactory;

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function projects(){
        return $this->hasMany(Project::class);
    }





    // Project has budget division
    // Personel Service PS
    //  Budget Division Category
            //Direct Cost
              // Budget Expenses
                // --- Title
            //Indirect Cost
    // MOOE
    // EO


    // RELATIONSHIPS
    //  -- BudgetDivision
    // Project has many BudgetDivision
    // ProjectDivision belongs to projects
    // BudgetDivision has many BudgetDivisionCategory

    // Example
        // Personal Service


    // -- BudgetDivisionCategory
    // BudgetDivisionCategory has many BudgetExpenses
    // BudgetDivisionCategory belongs to BudgetDivision
    // Example
       // Personal Service
           // Direct Cost
           // Indirect Cost

    // -- BudgetDivisionExpenses
    // BudgetDivisionExpenses belong to BudgetDivisionCategory
    // BudgetDivisionExpenses has many

    // --  BudgetExpenses
     // BudgetExpenses belongs to Budget Division


    //  BudgetDivision
    //         BudgetDivisionCategory
    //             BudgetDivisionExpenses
    //                 BudgetExpenses


    // RESULT WILL BE

    // Personal Services
    //     Direct Cost
    //         - Salaries
    //             - One (1) Project Technical Aide VI @ 25,355.00/mo



      //ProjectQuarter
       // QuaterExpenses

            //BudgetExpenses


        //1. User will select the year
        //2. user will select Quarter
        //3. user can select either which repeater
        //


        // Year
        // PS $year->quarters->sum('quarer1')
        // PS $year->quarters->sum('quarer2')
        // PS $year->quarters->sum('quarer3')


        // public function solution(){

        //     $budget_years = [];

        //     foreach($budget_years as $budget_year){
        //             @foreach($quar)
        //     }

        // }



            // final soution is

            // ProjectQuarter
                // ProjectQuarterExpenses
                //    has amount
                //    belongs to project expenses

                // and re arange the relationship back to the originala again


}
