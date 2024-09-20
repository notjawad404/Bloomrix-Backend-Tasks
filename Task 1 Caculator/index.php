<?php
$num = "";
$result = "";
$expression = "";
$resetInput = false;

if (isset($_POST['num'])) {
   
    if (isset($_POST['resetInput']) && $_POST['resetInput'] == "true") {
        $num = $_POST['num']; 
        $resetInput = false;
    } else {
        $num = $_POST['input'] . $_POST['num'];
    }
}

if (isset($_POST['op'])) {
    $num = $_POST['input'] . " " . $_POST['op'] . " ";
    $resetInput = false;
}


if (isset($_POST['cancel'])) {
    $num = "";
    $resetInput = false;
}

if (isset($_POST['equal'])) {
    try {
        $expression = $_POST['input'];
        $result = eval("return $expression;");
        $num = $result;
        $resetInput = true;
    } catch (Exception $e) {
        $num = "Error";
        $resetInput = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="calc">
        <form action="" method="post">
            <!-- Hidden field to track if resetInput is true -->
            <input type="hidden" name="resetInput" value="<?php echo $resetInput ? 'true' : 'false'; ?>">
            
            <input type="text" class="maininput" name="input" value="<?php echo htmlspecialchars($num); ?>" readonly>
            <div class="button-grid">
                <input type="submit" class="btn numbtn" name="num" value="7">
                <input type="submit" class="btn numbtn" name="num" value="8">
                <input type="submit" class="btn numbtn" name="num" value="9">
                <input type="submit" class="btn calbtn" name="op" value="+">
                <input type="submit" class="btn numbtn" name="num" value="4">
                <input type="submit" class="btn numbtn" name="num" value="5">
                <input type="submit" class="btn numbtn" name="num" value="6">
                <input type="submit" class="btn calbtn" name="op" value="-">
                <input type="submit" class="btn numbtn" name="num" value="1">
                <input type="submit" class="btn numbtn" name="num" value="2">
                <input type="submit" class="btn numbtn" name="num" value="3">
                <input type="submit" class="btn calbtn" name="op" value="*">
                <input type="submit" class="btn cancel" name="cancel" value="C">
                <input type="submit" class="btn numbtn" name="num" value="0">
                <input type="submit" class="btn equal" name="equal" value="=">
                <input type="submit" class="btn calbtn" name="op" value="/">
            </div>
        </form>
    </div>
</body>
</html>
