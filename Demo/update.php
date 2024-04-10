<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$id = $first_name = $last_name = $email = $DOB = $gender = $contact = $departure_city = $destination_city = "";
$id_err = $first_name_err = $last_name_err = $email_err = $DOB_err = $gender_err = $contact_err = $departure_city_err = $destination_city_err = "";

// Processing form data when form is submitted
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    // Get hidden input value
    $id = $_POST["id"];

    // Validate first name
    $input_name = trim($_POST["first_name"]);
    if (empty($input_first_name)) {
        $first_name_err = "Please enter a name.";
    } elseif (!filter_var($input_first_name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
        $first_name_err = "Please enter a valid first name.";
    } else {
        $first_name = $input_first_name;
    }

    // Validate last name
    $input_last_name = trim($_POST["last_name"]);
    if (empty($input_last_name)) {
        $last_name_err = "Please enter last name.";
    } else {
        $last_name = $input_last_name;
    }

    // Validate E-mail
    $input_email = trim($_POST["email"]);
    if (empty($input_email)) {
        $email_err = "Please enter the e-mail.";
    } else {
        $email = $input_email;
    }

     // Validate D.O.B
     $input_DOB = trim($_POST["DOB"]);
     if (empty($input_DOB)) {
         $DOB_err = "Please enter the DOB.";
     } else {
         $DOB = $input_DOB;
     }

      // Validate Gender
    $input_gender = trim($_POST["gender"]);
    if (empty($input_gender)) {
        $gender_err = "Please enter the gender.";
    } else {
        $gender = $input_gender;
    }

     // Validate Contact
     $input_contact = trim($_POST["contact"]);
     if (empty($input_contact)) {
         $contact_err = "Please enter the contact number.";
     } else {
         $contact = $input_contact;
     }

      // Validate Departure city
    $input_departure_city = trim($_POST["departure_city"]);
    if (empty($input_departure_city)) {
        $departure_city_err = "Please enter the departure city.";
    } else {
        $departure_city = $input_departure_city;
    }

     // Validate Destination city
     $input_destination_city = trim($_POST["destination_city"]);
     if (empty($input_destination_city)) {
         $destination_city_err = "Please enter the destination city.";
     } else {
         $destination_city = $input_destination_city;
     }
 
    // Check input errors before inserting in database
    if (empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($DOB_err) && empty($gender_err) && empty($contact_err) && empty($departure_city_err) && empty($destination_city_err)) {
        // Prepare an update statement
        $sql = "UPDATE cutomer SET first_name=?, last_name=?, email=?, DOB=?, gender=?, contact=?, departure_city=?, destination_city=?, WHERE id=?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssssi", $param_first_name, $param_last_name, $param_email, $param_DOB, $param_gender, $param_contact, $param_departure_city, $param_destination_city, $param_id);

            // Set parameters
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_email = $email;
            $param_DOB = $DOB;
            $param_gender = $gender;
            $param_contact = $contact;
            $param_departure_city = $departure_city;
            $param_destination_city = $destination_city;
            $param_id = $id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
} else {
    // Check existence of id parameter before processing further
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        // Get URL parameter
        $id =  trim($_GET["id"]);

        // Prepare a select statement
        $sql = "SELECT * FROM customer_details WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);

            // Set parameters
            $param_id = $id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    // Retrieve individual field value
                    $first_name = $row["first_name"];
                    $last_name = $row["last_name"];
                    $email = $row["email"];
                    $DOB = $row["DOB"];
                    $gender = $row["gender"];
                    $contact = $row["contact"];
                    $departure_city = $row["departure_city"];
                    $destination_city = $row["destination_city"];
                } else {
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);

        // Close connection
        mysqli_close($link);
    } else {
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
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper {
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
                    <h2 class="mt-5">Update Record</h2>
                    <p>Please edit the input values and submit to update the employee record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $first_name; ?>">
                            <span class="invalid-feedback"><?php echo $first_name_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <textarea name="last_name" class="form-control <?php echo (!empty($last_name_err)) ? 'is-invalid' : ''; ?>"><?php echo $last_name; ?></textarea>
                            <span class="invalid-feedback"><?php echo $last_name_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>E-mail</label>
                            <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>D.O.B</label>
                            <input type="text" name="DOB" class="form-control <?php echo (!empty($DOB_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $DOB; ?>">
                            <span class="invalid-feedback"><?php echo $DOB_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <input type="text" name="gender" class="form-control <?php echo (!empty($gender_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $gender; ?>">
                            <span class="invalid-feedback"><?php echo $gender_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Contact</label>
                            <input type="text" name="contact" class="form-control <?php echo (!empty($contact_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $contact; ?>">
                            <span class="invalid-feedback"><?php echo $contact_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Departure city</label>
                            <input type="text" name="departure_city" class="form-control <?php echo (!empty($departure_city_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $departure_city; ?>">
                            <span class="invalid-feedback"><?php echo $departure_city_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Destination city</label>
                            <input type="text" name="destination_city" class="form-control <?php echo (!empty($destinaion_city_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $destination_city; ?>">
                            <span class="invalid-feedback"><?php echo $destination_city_err; ?></span>
                        
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>