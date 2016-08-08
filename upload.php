<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

session_set_cookie_params(0);
session_start();

if (!isset($_SESSION['id'])) {
    $responseJson['status'] = array(
        "statusCode" => 808,
        "status" => "Authentication",
        "Description" => "Authentication Failed"
    );
    echo json_encode($responseJson);
    return;
}

$responseJson = array();
$responseJson['status'] = null;
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);


date_default_timezone_set('Europe/London');

/** Include PHPExcel_IOFactory */
require_once dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';

// target directory to store file
$target_dir = "uploads/";

// this will return error if there is no file
if (!isset($_FILES["file"]["name"])) {
    $responseJson['status'] = array(
        "statusCode" => 807,
        "status" => "Error Uploading file",
        "Description" => "Please check file is correctly uploading and file size is not 0"
    );

    echo json_encode($responseJson);
    return;
}

$target_file = $target_dir . basename($_FILES["file"]["name"]);
$fileName = pathinfo($target_file,PATHINFO_EXTENSION);

// check if file is a spreadsheet
if($fileName != "xls" && $fileName != "xlsx") {
    // todo: make return a status accordingly
    $responseJson['status'] = array(
        "statusCode" => 801,
        "status" => "Error invalid file",
        "Description" => "It seems its not spreadsheet please make sure uploading right file"
    );
    echo json_encode($responseJson);
    return;

}


if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
//    echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
} else {
    $responseJson['status'] = array(
        "statusCode" => 807,
        "status" => "Error Uploading file",
        "Description" => "Please check file is correctly uploading and file size is not 0"
    );
    echo json_encode($responseJson);
    return;
}

// check to find existence of file
if (!file_exists($target_file)) {
    // response JSON data to pass error to client side
    $responseJson['status'] = array(
        "statusCode" => 800,
        "status" => "Error uploading file",
        "Description" => "There is an error uploading file please try again or make sure file is readable and not corrupted"
    );
    echo json_encode($responseJson);
    return;

}

$objPHPExcel = null;

try {
    // try to open excel file
    $objPHPExcel = PHPExcel_IOFactory::load($target_file);

} catch (PHPExcel_Reader_Exception $e) {
    // this will tell client about file if file type is excel but not a valid excel
    $responseJson['status'] = array(
        "statusCode" => 801,
        "status" => "Error invalid file",
        "Description" => "It seems its not spreadsheet please make sure uploading right file"
    );
    echo json_encode($responseJson);
    return;
}

// chartTitle will contain chart Title from sheet#1 if does ot exist it will be untitled
$chartTitle = 'untitled';

// an exception will thrown if there is no sheet present in excel i.e if index 0 doesn't exist
try {
    // getTitle tells the sheet title
    if ($objPHPExcel->getSheet(0)->getTitle() == 'title') {

        $chartTitle = $objPHPExcel->getSheet(0)->getCell()->getValue();

        if ($chartTitle == "")
            $chartTitle = 'untitled';
    } else {
        $chartTitle = 'untitled';
    }
} catch (PHPExcel_Exception $e) {
    $responseJson['status'] = array(
        "statusCode" => 802,
        "status" => "Error invalid data",
        "Description" => "It seems spreadsheet is empty"
    );

    echo json_encode($responseJson);
    return;
}

try {
    // getSheet # 2. if its name is 'data'  we will move further or
    // if sheet 2 does not exist or name is not 'data' it will send error to client
    if ($objPHPExcel->getSheet(1)->getTitle() != 'data') {

        $responseJson['status'] = array(
            "statusCode" => 806,
            "status" => "Error data not found",
            "Description" => "Data Sheet not found at second sheet. make sure data sheet is present and named 'data'"
        );

        echo json_encode($responseJson);
        return;
    }

    $dataSheet = $objPHPExcel->getSheet(1);

    // we are just considering data must be start from A1 and B1 otherwise it will send an error
    // these two variables contains array having two indexes for row and column
    $coordinatesCount = ['A', 1];
    $coordinatesName = ['B', 1];

    // populate json response
    $responseJson['chart'] = array("title" => $chartTitle);

    $responseJson['data'] = array();
    $i = 1; // iterator i to iterate in cells of data
    $j = 0; // iterator to populate response data array if data is valid on ith index

    $count = $dataSheet->getCell($coordinatesCount[0] . ($coordinatesCount[1] + $i));
    $name = $dataSheet->getCell($coordinatesName[0] . ($coordinatesName[1] + $i));

    $dataFound = false;
    // loop for populate the data into response array
    for ($i = 2; $name->getValue() != "" && $count->getValue() != "" ; $i++) {

        // check if data is valid for chart i.e count is numeric and has some valid name
        if (is_numeric($count->getValue()) && $name->getValue() != "") {
            $responseJson['data'][$j++] = ["title" => $name->getValue(), "value" => $count->getValue(), "color" => random_color()];
            $dataFound = true;
        }
        $count = $dataSheet->getCell($coordinatesCount[0] . ($coordinatesCount[1] + $i));
        $name = $dataSheet->getCell($coordinatesName[0] . ($coordinatesName[1] + $i));

    }
    if ($dataFound) {
        // check whether we have valid data found or not if found we will send OK status
        $responseJson['status'] = array("statusCode" => 200, "status" => "OK");
    } else {
        $responseJson['status'] = array(
            "statusCode" => 804,
            "status" => "Not valid data",
            "Description" => "data under pi chart labels is not valid or empty"
        );

    }

} catch (PHPExcel_Exception $e) {
    $responseJson['status'] = array(
        "statusCode" => 803,
        "status" => "Error invalid file",
        "Description" => "Data Sheet not found make sure about second sheet is present and named 'data'"
        );

    echo json_encode($responseJson);
    return;
}

echo json_encode($responseJson);


/**
 * this function will return a unique color code from 0-255
 * @return string the random color code between 0-255 in string format
 */
function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

/**
 * function will return a random 24 bit color code.
 * @return string return the color code in '#f0f0f0' format
 */
function random_color() {
    return '#' . random_color_part() . random_color_part() . random_color_part();
}