php -S localhost:8000


<?php
// Function to generate SVG for a task bar
function generateTaskSVG($x, $y, $width, $height, $taskName) {
    $svg = '<rect x="' . $x . '" y="' . $y . '" width="' . $width . '" height="' . $height . '" fill="#7FB3D5" />';
    $svg .= '<text x="' . ($x + 5) . '" y="' . ($y + $height / 2) . '" fill="black">' . $taskName . '</text>';
    return $svg;
}

// Function to generate SVG for a dependency line
function generateDependencySVG($x1, $y1, $x2, $y2) {
    $svg = '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="black" />';
    return $svg;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process input data
    $tasks = $_POST['tasks'];
    $startDates = $_POST['start_dates'];
    $endDates = $_POST['end_dates'];
    $dependencies = $_POST['dependencies'];

    // Generate Gantt chart
    $svg = '<svg width="800" height="400">';
    $barHeight = 30;
    $barPadding = 10;
    $currentY = 50;

    for ($i = 0; $i < count($tasks); $i++) {
        // Calculate bar position and width based on start and end dates
        $startDate = strtotime($startDates[$i]);
        $endDate = strtotime($endDates[$i]);
        $x = ($startDate - strtotime('today')) / (60 * 60 * 24) * 20;
        $width = ($endDate - $startDate) / (60 * 60 * 24) * 20;

        // Generate SVG for task bar
        $svg .= generateTaskSVG($x, $currentY, $width, $barHeight, $tasks[$i]);

        // Check for dependencies
        if (!empty($dependencies[$i])) {
            // Draw dependency lines
            $dependencyTasks = explode(',', $dependencies[$i]);
            foreach ($dependencyTasks as $dependencyTask) {
                $dependencyIndex = (int)$dependencyTask - 1;
                $dependencyX = ($dependencyIndex < $i) ? ($x - $barPadding) : ($x + $width + $barPadding);
                $dependencyY = $currentY + $barHeight / 2;
                $svg .= generateDependencySVG($dependencyX, $dependencyY, $x, $currentY + $barHeight / 2);
            }
        }

        $currentY += $barHeight + $barPadding;
    }

    $svg .= '</svg>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gantt Chart Generator</title>
    <style>
        svg {
    border: 1px solid #ccc;
}

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}
.container {
    max-width: 70%;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
h1 {
    text-align: center;
}
form {
    margin-bottom: 20px;
}
label {
    display: block;
    margin-bottom: 5px;
}
input[type="text"],
input[type="date"] {
    width: calc(100% - 12px);
    padding: 6px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
    box-sizing: border-box;
}
input[type="text"]:focus,
input[type="date"]:focus {
    outline: none;
    border-color: #66afe9;
}
button {
    padding: 8px 16px;
    background-color: #4caf50;
    color: #fff;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}
button:hover {
    background-color: #45a049;
}
.gantt-chart {
    border: 1px solid #ccc;
    border-radius: 5px;
    overflow-x: auto;
}
    </style>
</head>
<body>
    <div class="container">
        <h1>Gantt Chart Generator</h1>
        <form method="post">
            <label for="tasks">Task Name:</label>
            <input type="text" id="tasks" name="tasks[]" required>
            <label for="start_dates">Start Date:</label>
            <input type="date" id="start_dates" name="start_dates[]" required>
            <label for="end_dates">End Date:</label>
            <input type="date" id="end_dates" name="end_dates[]" required>
            <label for="dependencies">Dependencies (comma-separated task numbers):</label>
            <input type="text" id="dependencies" name="dependencies[]">
            <br><br>
            <button type="button" onclick="addTask()">Add Task</button>
            <button type="submit">Generate Chart</button>
        </form>
        <div class="gantt-chart">
            <?php echo isset($svg) ? $svg : ''; ?>
        </div>
    </div>

    <script>
        function addTask() {
            const form = document.querySelector('form');
            const taskInput = document.createElement('input');
            taskInput.type = 'text';
            taskInput.name = 'tasks[]';
            taskInput.required = true;
            taskInput.placeholder = 'Task Name';
            
            const startDateInput = document.createElement('input');
            startDateInput.type = 'date';
            startDateInput.name = 'start_dates[]';
            startDateInput.required = true;

            const endDateInput = document.createElement('input');
            endDateInput.type = 'date';
            endDateInput.name = 'end_dates[]';
            endDateInput.required = true;

            const dependencyInput = document.createElement('input');
            dependencyInput.type = 'text';
            dependencyInput.name = 'dependencies[]';
            dependencyInput.placeholder = 'Dependencies (comma-separated task numbers)';

            form.insertBefore(document.createElement('br'), form.lastElementChild);
            form.insertBefore(dependencyInput, form.lastElementChild);
            form.insertBefore(document.createElement('br'), form.lastElementChild);
            form.insertBefore(endDateInput, form.lastElementChild);
            form.insertBefore(document.createElement('br'), form.lastElementChild);
            form.insertBefore(startDateInput, form.lastElementChild);
            form.insertBefore(document.createElement('br'), form.lastElementChild);
            form.insertBefore(taskInput, form.lastElementChild);
            form.insertBefore(document.createElement('br'), form.lastElementChild);
        }
    </script>
</body>
</html>



