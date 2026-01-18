<?php
session_start();

// Initialize courses
if(!isset($_SESSION['courses'])){
    $_SESSION['courses'] = [];
}

// UP Grade Conversion
function convertToUPGrade($percent){
    if($percent>=97) return 1.00;
    if($percent>=94.25) return 1.25;
    if($percent>=91.50) return 1.50;
    if($percent>=88.75) return 1.75;
    if($percent>=86.00) return 2.00;
    if($percent>=83.25) return 2.25;
    if($percent>=80.50) return 2.50;
    if($percent>=77.75) return 2.75;
    if($percent>=75.00) return 3.00;
    if($percent<75.00) return 4.00;
    return 5.00;
}

// Add new course
if(isset($_POST['add_course'])){
    $code = trim($_POST['course_code']);
    if($code!==""){
        $_SESSION['courses'][] = [
            'code'=>$code,
            'activities'=>[],
            'units'=>3
        ];
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Remove course
if(isset($_POST['remove_course'])){
    $idx = $_POST['remove_course'];
    if(isset($_SESSION['courses'][$idx])){
        array_splice($_SESSION['courses'],$idx,1);
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Auto-save (live save from JS)
if(isset($_POST['auto_save'])){
    $idx = $_POST['course_index'];
    $activities = [];
    if(isset($_POST['activity']) && is_array($_POST['activity'])){
        for($i=0;$i<count($_POST['activity']);$i++){
            if($_POST['activity'][$i]==='') continue;
            $activities[] = [
                'name'=>$_POST['activity'][$i],
                'score'=>floatval($_POST['score'][$i] ?? 0),
                'max'=>floatval($_POST['max'][$i] ?? 0),
                'weight'=>floatval($_POST['weight'][$i] ?? 0)
            ];
        }
    }
    $_SESSION['courses'][$idx]['activities'] = $activities;
    $_SESSION['courses'][$idx]['units'] = floatval($_POST['units'] ?? 3);
    exit; // stop page reload
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>TeiGwaCalc</title>
<style>
body{font-family:Arial,sans-serif;padding:20px;}
table{border-collapse:collapse;width:100%;margin-bottom:10px;}
table,th,td{border:1px solid #ccc;}
th,td{padding:6px;text-align:center;}
.weight-warning{color:red;display:none;}
.course-card{border:1px solid #aaa;padding:15px;margin-bottom:20px;position:relative;}
button{margin-right:5px;}
.remove-course-btn{position:absolute;top:10px;right:10px;}
</style>
</head>
<body>

<h2>Tei's UP GWA Calculator</h2>

<!-- Add Course -->
<form method="post">
    <input type="text" name="course_code" placeholder="Course Code (e.g. CS 11)" required>
    <button type="submit" name="add_course">Add Course</button>
</form>
<hr>

<!-- Courses -->
<?php foreach($_SESSION['courses'] as $idx=>$course): ?>
<div class="course-card">
    <h3><?= htmlspecialchars($course['code']) ?></h3>

    <!-- Remove Course Form -->
    <form method="post" style="display:inline;">
        <input type="hidden" name="remove_course" value="<?= $idx ?>">
        <button type="submit" class="remove-course-btn">Remove Course ❌</button>
    </form>

    <!-- Auto-save form for activities -->
    <form class="activity-form">
        <input type="hidden" name="course_index" value="<?= $idx ?>">
        <p>Units: <input type="number" name="units" value="<?= $course['units'] ?>" min="1"></p>

        <table>
            <thead>
                <tr>
                    <th>Activity</th>
                    <th>Score</th>
                    <th>Max</th>
                    <th>Weight (%)</th>
                    <th>Raw %</th>
                    <th>Weighted %</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $activities = $course['activities'];
                if(empty($activities)) $activities[]=['name'=>'','score'=>0,'max'=>0,'weight'=>0];
                foreach($activities as $i=>$act):
                    $score=floatval($act['score']);
                    $max=floatval($act['max']);
                    $weight=floatval($act['weight']);
                    $raw = $max>0?($score/$max)*100:0;
                    $weighted = ($raw/100)*$weight;
                ?>
                <tr>
                    <td><input type="text" name="activity[]" value="<?= htmlspecialchars($act['name']) ?>"></td>
                    <td><input type="number" name="score[]" value="<?= $act['score'] ?>"></td>
                    <td><input type="number" name="max[]" value="<?= $act['max'] ?>"></td>
                    <td><input type="number" name="weight[]" value="<?= $act['weight'] ?>"></td>
                    <td><?= number_format($raw,2) ?>%</td>
                    <td><?= number_format($weighted,2) ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p>Total Weight: <strong class="weight-total">0%</strong> 
        <span class="weight-warning">Total weight must be 100%</span></p>
        <p class="final-grade">Final Grade: 0% → UP Grade: 0</p>
        <button type="button" class="add-activity-btn">+ Add Activity</button>
    </form>
</div>

<?php endforeach; ?>

<!-- Live GWA -->
<h3 id="live-gwa"></h3>

<script>
// Grade conversion
function convertToUPGrade(percent){
    if(percent>=97) return 1.00;
    if(percent>=94.25) return 1.25;
    if(percent>=91.5) return 1.5;
    if(percent>=88.75) return 1.75;
    if(percent>=86) return 2.0;
    if(percent>=83.25) return 2.25;
    if(percent>=80.5) return 2.5;
    if(percent>=77.75) return 2.75;
    if(percent>=75) return 3.0;
    if(percent<75) return 4.0;
    return 5.0;
}

// Update single row
function updateRow(row){
    const score = parseFloat(row.querySelector('input[name="score[]"]').value)||0;
    const max = parseFloat(row.querySelector('input[name="max[]"]').value)||0;
    const weight = parseFloat(row.querySelector('input[name="weight[]"]').value)||0;
    const raw = max>0?(score/max)*100:0;
    const weighted = (raw/100)*weight;
    row.cells[4].textContent = raw.toFixed(2)+"%";
    row.cells[5].textContent = weighted.toFixed(2)+"%";
}

// Update course totals and auto-save
function updateCourse(form){
    const rows = form.querySelectorAll('tbody tr');
    let totalWeight=0, finalPercent=0;
    rows.forEach(row=>{
        updateRow(row);
        totalWeight += parseFloat(row.querySelector('input[name="weight[]"]').value)||0;
        finalPercent += parseFloat(row.cells[5].textContent)||0;
    });
    form.querySelector('.weight-total').textContent = totalWeight+'%';
    form.querySelector('.weight-warning').style.display = totalWeight!==100?"inline":"none";
    form.querySelector('.final-grade').textContent = 'Final Grade: '+finalPercent.toFixed(2)+'% → UP Grade: '+convertToUPGrade(finalPercent).toFixed(2);

    updateGWA();
    autoSave(form);
}

// Live GWA
function updateGWA(){
    const forms = document.querySelectorAll('.activity-form'); // only activity forms
    let totalUnits=0, weightedSum=0;
    forms.forEach(f=>{
        const units = parseFloat(f.querySelector('input[name="units"]').value)||3;
        let finalPercent=0;
        f.querySelectorAll('tbody tr').forEach(r=>{
            finalPercent += parseFloat(r.cells[5].textContent)||0;
        });
        weightedSum += convertToUPGrade(finalPercent)*units;
        totalUnits += units;
    });
    document.querySelector('#live-gwa').textContent = totalUnits>0
        ?"General Weighted Average (GWA): "+(weightedSum/totalUnits).toFixed(2)
        :"Cannot compute GWA: ensure all courses have total weight.";
}

// Auto-save course via fetch
function autoSave(form){
    const fd = new FormData(form);
    fd.append("auto_save","1");
    fetch("",{method:"POST",body:fd});
}

// Listen to input changes
document.addEventListener("input", e=>{
    if(['score[]','max[]','weight[]','units','activity[]'].includes(e.target.name)){
        updateCourse(e.target.closest("form"));
    }
});

// Add activity button
document.addEventListener("click", e=>{
    if(!e.target.classList.contains('add-activity-btn')) return;
    const form = e.target.closest('form');
    const tbody = form.querySelector('tbody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td><input type="text" name="activity[]" value=""></td>
        <td><input type="number" name="score[]" value=""></td>
        <td><input type="number" name="max[]" value=""></td>
        <td><input type="number" name="weight[]" value=""></td>
        <td>0.00%</td>
        <td>0.00%</td>
    `;
    tbody.appendChild(newRow);
    updateCourse(form);
    newRow.scrollIntoView({behavior:"smooth"});
});

// Initialize
document.querySelectorAll('.activity-form').forEach(f=>updateCourse(f));
</script>

</body>
</html>
