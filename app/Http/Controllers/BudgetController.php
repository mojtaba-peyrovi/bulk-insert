<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Budget;

class BudgetController extends Controller
{
    public function showForm(){
        return view('upload');
    }

    public function store(Request $request){

        //get file
            $upload = $request->file('upload-file');
            $filePath = $upload->getRealPath();
        //open and read
            $file = fopen($filePath,'r'); // r means READ
            $header = fgetcsv($file); // reads the file
        //dd($header);
        $escapedHeader = [];
        //validate
            foreach ($header as $key => $value) {
                $lowerCasedHeader = strtolower($value);
                $escapedItem = preg_replace('/[[^a-z]]/','',$lowerCasedHeader);  // it removes any special characters from the headers exceptp a-z letters
            //dd($escapedItem);
                array_push($escapedHeader,$escapedItem);
            }
            //dd($escapedHeader);

            //looping through columns
            while($columns=fgetcsv($file)) {
                if($columns[0]==""){
                    continue;
                }
                //trim data
                foreach ($columns as $key => &$value) {
                    $value = preg_replace('/\D/','',$value);
                }
                //dd($value);
                $data = array_combine($escapedHeader, $columns);

                //setting data type
                foreach ($data as $key => &$value) {
                    $value =($key=="zip" || $key =="month")?(integer)$value: (float)$value;
                }

                //table update
                $zip = $data['zip'];
                $month = $data['month'];
                $lodging = $data['lodging'];
                $meal = $data['meal'];
                $housing = $data['housing'];

                $budget = Budget::firstOrNew(['zip'=> $zip,'month'=>$month]);
                $budget->lodging = $lodging;
                $budget->meal = $meal;
                $budget->housing = $housing;
                $budget->save();
            }
    }
}
