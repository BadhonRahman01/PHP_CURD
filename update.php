<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$store_id = $branch_name = $branch_location = $phone = $lat = $long = "";
$store_id_err = $branch_name_err = $branch_location_err = $phone_err = $lat_err = $long_err = ""; 
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
// Validate store id
$input_store_id = trim($_POST["store_id"]);
if(empty($input_store_id)){
    $store_id_err = "Please enter a Store ID";     
} else{
    $store_id = $input_store_id;
}

// Validate branch name

$input_branch_name = trim($_POST["branch_name"]);
if(empty($input_branch_name)){
    $branch_name_err = "Please enter a store branch name.";
} elseif(!filter_var($input_branch_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
    $branch_name_err = "Please enter a valid store name";
} else{
    $branch_name = $input_branch_name;
}
// Validate branch location
$input_branch_location = trim($_POST["branch_location"]);
if(empty($input_branch_location)){
    $branch_location_err = "Please enter a branch address.";     
} else{
    $branch_location = $input_branch_location;
}

// Validate phone
$input_phone = trim($_POST["phone"]);
if(empty($input_phone)){
    $phone_err = "Please enter the phone number";     
} elseif(!ctype_digit($input_phone)){
    $phone_err = "Please enter a valid phone number.";
} else{
    $phone = $input_phone;
}

// Validate lat
$input_lat = trim($_POST["lat"]);
if(empty($input_lat)){
    $lat_err = "Please enter latitude of this branch";     
} else{
    $lat = $input_lat;
}

    // Validate long
    $input_long = trim($_POST["long"]);
    if(empty($input_long)){
        $long_err = "Please enter a longitude for this branch";     
    } else{
        $long = $input_long;
    }

    
    // Check input errors before inserting in database
    if(empty($store_id_err) && empty($branch_name_err) && empty($branch_location_err) && empty($phone_err) && empty($lat_err) && empty($long_err)){
        // Prepare an update statement
        $sql = "UPDATE stores SET store_id=?, branch_name=?, branch_location=?,phone=?, lat=?, long=? WHERE id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssi", $param_store_id, $param_branch_name, $param_branch_location,$param_phone, $param_lat, $param_long);
            
            // Set parameters
            $param_store_id = $store_id;
            $param_branch_name = $branch_name;
            $param_branch_location = $branch_location;
            $param_phone = $phone;
            $param_lat = $lat;
            $param_long = $long;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
                    // Close statement
        mysqli_stmt_close($stmt);
        }
         

    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM stores WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $store_id = $row["store_id"];
                    $branch_name = $row["branch_name"];
                    $branch_location = $row["branch_location"];
                    $phone = $row["phone"];
                    $lat = $row["lat"];
                    $long = $row["long"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
                    // Close statement
        mysqli_stmt_close($stmt);
        }
        

        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Store</h2>
                    <p>Please edit the input values and submit to update the store record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                    <div class="form-group">
                            <label>Store ID</label>
                            <input type="number" name="store_id" class="form-control <?php echo (!empty($store_id_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $store_id; ?>">
                            <span class="invalid-feedback"><?php echo $store_id_err;?></span>
                        </div>    
                    <div class="form-group">
                            <label>Branch Name</label>
                            <input type="text" name="branch_name" class="form-control <?php echo (!empty($branch_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $branch_name; ?>">
                            <span class="invalid-feedback"><?php echo $branch_name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Branch Location</label>
                            <textarea name="branch_location" class="form-control <?php echo (!empty($branch_location_err)) ? 'is-invalid' : ''; ?>"><?php echo $branch_location; ?></textarea>
                            <span class="invalid-feedback"><?php echo $branch_location_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>">
                            <span class="invalid-feedback"><?php echo $phone_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Branch Latitude</label>
                            <input type="text" name="lat" class="form-control <?php echo (!empty($lat_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $lat; ?>">
                            <span class="invalid-feedback"><?php echo $lat_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Branch longitude</label>
                            <input type="text" name="long" class="form-control <?php echo (!empty($long_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $long; ?>">
                            <span class="invalid-feedback"><?php echo $long_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>