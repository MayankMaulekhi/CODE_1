<?php
$field_types = [
  "name" => "Name (Text Field)",
  "email" => "Email",
  "password" => "Password",
  "gender" => "Gender (Dropdown)",
  "subscribe" => "Subscribe (Checkbox)"
];
// Dropdown options
$dropdown_options = ["Male", "Female", "Other", "Prefer not to say"];

$selected_fields = $_POST['selected_fields'] ?? [];
$show_form = isset($_POST['select_fields']) || isset($_POST['submit_form']);
$validation_feedback = [];
$form_values = [];
$success_message = "";

if (isset($_POST['submit_form'])) {
  foreach ($selected_fields as $field) {
    // Get value and validate
    switch($field) {
      case "name":
        $form_values[$field] = trim($_POST[$field] ?? "");
        if ($form_values[$field] === "" || !preg_match("/^[A-Za-z ]+$/", $form_values[$field])) {
          $validation_feedback[$field] = "Enter a valid name (letters only).";
        }
        break;
      case "email":
        $form_values[$field] = trim($_POST[$field] ?? "");
        if (!filter_var($form_values[$field], FILTER_VALIDATE_EMAIL)) {
          $validation_feedback[$field] = "Enter a valid email.";
        }
        break;
      case "password":
        $form_values[$field] = $_POST[$field] ?? "";
        if (strlen($form_values[$field]) < 6) {
          $validation_feedback[$field] = "Password must be at least 6 characters.";
        }
        break;
      case "gender":
        $form_values[$field] = $_POST[$field] ?? "";
        if (!in_array($form_values[$field], $dropdown_options)) {
          $validation_feedback[$field] = "Select a valid gender.";
        }
        break;
      case "subscribe":
        $form_values[$field] = isset($_POST[$field]) ? "Yes" : "No";
        break;
    }
  }
  if (!$validation_feedback) {
    file_put_contents("responses.txt", json_encode($form_values)."\n", FILE_APPEND);
    $success_message = "Form submitted and saved!";
    $form_values = [];
    $show_form = false;
    $selected_fields = [];
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dynamic PHP Form Generator</title>
</head>
<body>
<h2>Dynamic PHP Form Generator</h2>
<!-- Field Selection Form -->
<?php if (!$show_form): ?>
<form method="post">
  <h3>Select the fields required for your form:</h3>
  <?php foreach ($field_types as $key => $label): ?>
    <label>
      <input type="checkbox" name="selected_fields[]" value="<?= htmlspecialchars($key) ?>">
      <?= htmlspecialchars($label) ?>
    </label><br>
  <?php endforeach; ?>
  <button type="submit" name="select_fields">Create Form</button>
</form>
<?php endif; ?>

<!-- Dynamic Form -->
<?php if ($show_form && $selected_fields): ?>
<form method="post">
  <h3>Fill in the Form:</h3>
  <?php foreach ($selected_fields as $field):
    switch ($field):
      case "name": ?>
        Name: <input type="text" name="name" value="<?= htmlspecialchars($form_values["name"] ?? "") ?>">
        <span style="color:red"><?= $validation_feedback["name"] ?? "" ?></span>
        <br><br>
        <?php break;
      case "email": ?>
        Email: <input type="text" name="email" value="<?= htmlspecialchars($form_values["email"] ?? "") ?>">
        <span style="color:red"><?= $validation_feedback["email"] ?? "" ?></span>
        <br><br>
        <?php break;
      case "password": ?>
        Password: <input type="password" name="password" value="">
        <span style="color:red"><?= $validation_feedback["password"] ?? "" ?></span>
        <br><br>
        <?php break;
      case "gender": ?>
        Gender:
        <select name="gender">
          <option value="">Select</option>
          <?php foreach ($dropdown_options as $opt): ?>
            <option value="<?= htmlspecialchars($opt) ?>" <?= (isset($form_values["gender"]) && $form_values["gender"]==$opt) ? "selected" : "" ?>><?= htmlspecialchars($opt) ?></option>
          <?php endforeach; ?>
        </select>
        <span style="color:red"><?= $validation_feedback["gender"] ?? "" ?></span>
        <br><br>
        <?php break;
      case "subscribe": ?>
        Subscribe to newsletter: <input type="checkbox" name="subscribe" <?= (isset($form_values["subscribe"]) && $form_values["subscribe"]=="Yes") ? "checked" : "" ?>>
        <br><br>
        <?php break;
    endswitch;
  endforeach;
  foreach ($selected_fields as $sf) {
    echo '<input type="hidden" name="selected_fields[]" value="'.htmlspecialchars($sf).'">';
  }
  ?>
  <button type="submit" name="submit_form">Submit</button>
</form>
<?php endif; ?>

<?php if ($success_message): ?>
  <div style="color:green;"><?= htmlspecialchars($success_message) ?></div>
<?php endif; ?>
</body>
</html>
