<?php
session_start();

if (!isset($_SESSION['courses'])) {
    $_SESSION['courses'] = [];
}

function convertToUPGrade($percent)
{
    if ($percent >= 97) return 1.00;
    if ($percent >= 94.25) return 1.25;
    if ($percent >= 91.50) return 1.50;
    if ($percent >= 88.75) return 1.75;
    if ($percent >= 86.00) return 2.00;
    if ($percent >= 83.25) return 2.25;
    if ($percent >= 80.50) return 2.50;
    if ($percent >= 77.75) return 2.75;
    if ($percent >= 75.00) return 3.00;
    return 4.00;
}

function getKirbyMessage()
{
    $messages = [
        "I love you, Tei!",
        "with kirby.",
        "kirby is watching your grades.",
        "kirby believes in you!!!",
        "this is powered by kirby energy.",
        "kirby calculated this.",
        "no kirbies were harmed.",
        "trust the kirby process.",
        "kirby wants uno grades from u.",
        "pakain mo na lang kay kirby mga terror prof.",
        "uno na ba?",
        "I LOVE YOU BABYYYYY UNO NA YAN!!!!",
        "can kirby survive UP?",
        '<a href="https://www.youtube.com/watch?v=fhhz7l86KUU">https://www.youtube.com/watch?v=fhhz7l86KUU</a>',
        "hala bagsak...?",
        '<audio controls>
            <source src="assets/mp3/kirbyHi.mp3" type="audio/mpeg">
         </audio>',
         '<audio controls>
            <source src="assets/mp3/kirk.mp3" type="audio/mpeg">
         </audio>',
        '<audio controls>
            <source src="assets/mp3/kirbyPoyo.mp3" type="audio/mpeg">
         </audio>',
        "Kali wants cuddles.",
        "ay hindi po ako active sa ig, pa-follow na lang po sa github.",
        "did you ligo?",
        "kirby will suck up your depression.",
        "proud of you baby!!!",
        "make me coffeeeeeeeeeeeee plsssssssssssss",
        "this randomizer is actually just 7 lines of code.",
        "make kirby proud.",
        "MM.. turn it around... WICKED WITCHOIAU:S][23RDHLAGIUSDF",
        "what if for CS ka talaga.",
        "kirby wishes you luck.",
        "kirby is still proud of you kahit singko ka.",
        "summa na.",
        "mwa mwa mwa mwa mwa.",
        "kirby can do your readings!",
        "bobby is tired of being smushed.",
        "can we pls cuddle hihihihi.",
        "what if maging prof mo si kirby.",
        "kirkby.",
        "did you know kirby was originally named popopo in development?",
        "kirby doesnt like gum, cuz he doesnt have teeth, he cant chew. (lol)",
        "kirby did NOT code this.",
        "kirby doesnt have a skeleton.",
        "kirby loves nothing more than eating and sleeping, like you baby ko!",
        "kirby is a small pink spherical creature with two stubby arms (cute!)",
        "kirby can eat UP to end all suffering. (then you can study in STI instead hiewehwieh)",
        "babe what if im dedede and you are kirby in the next life.",
        "kirby can split into multiple kirbys!!!",
        '<iframe data-testid="embed-iframe" style="border-radius:12px" src="https://open.spotify.com/embed/track/3kLamwtehsN5A7DXXS1fAf?utm_source=generator&theme=0" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe><br>HEHE',
    ];

    return $messages[array_rand($messages)];
}

if (isset($_POST['add_course'])) {
    $code = trim($_POST['course_code']);
    if ($code !== "") {
        $_SESSION['courses'][] = [
            'code' => $code,
            'activities' => [],
            'units' => 3
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['remove_course'])) {
    array_splice($_SESSION['courses'], $_POST['remove_course'], 1);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['auto_save'])) {
    $idx = $_POST['course_index'];
    $activities = [];

    if (isset($_POST['activity'])) {
        foreach ($_POST['activity'] as $i => $name) {
            if ($name === '') continue;
            $activities[] = [
                'name' => $name,
                'score' => floatval($_POST['score'][$i] ?? 0),
                'max' => floatval($_POST['max'][$i] ?? 0),
                'weight' => floatval($_POST['weight'][$i] ?? 0)
            ];
        }
    }

    $_SESSION['courses'][$idx]['activities'] = $activities;
    $_SESSION['courses'][$idx]['units'] = floatval($_POST['units'] ?? 3);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TeiGwaCalc</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

    <header class="site-header">
        <div class="header-content">
            <img src="assets/picture/kirbySit.png" alt="NO IMG" class="header-img">
            <h1>Tei's UP GWA Calculator</h1>
            <p class="kirby-message"><?= getKirbyMessage(); ?></p>
        </div>
    </header>

    <form method="post" style="max-width:1000px; width:95%; margin:0 auto 15px; display:flex; gap:10px;">
        <input type="text" name="course_code" placeholder="Course Code (e.g. CS 11)" required>
        <button type="submit" name="add_course" class="btn-custom">
            Add Course
            <img
                class="kirby-hover"
                src="assets/picture/kirbyRun/kirbyRunningIdle.png"
                data-static="assets/picture/kirbyRun/kirbyRunningIdle.png"
                data-gif="assets/picture/kirbyRun/kirbyRunning.gif"
                alt="Kirby Running"
                width="40"
                height="40">
        </button>
    </form>


    <?php foreach ($_SESSION['courses'] as $idx => $course): ?>
        <div class="course-card">
            <div class="course-card-header">
                <h3 style="color: #ffffff;"><?= htmlspecialchars($course['code']) ?></h3>
                <form method="post">
                    <input type="hidden" name="remove_course" value="<?= $idx ?>">
                    <button class="remove-course-btn" onclick="return confirm('Remove this course?')">
                        Remove
                        <img
                            class="kirby-hover"
                            src="assets/picture/kirbyMeteor/meteorKirbyIdle.png"
                            data-static="assets/picture/kirbyMeteor/meteorKirbyIdle.png"
                            data-gif="assets/picture/kirbyMeteor/kirbyMeteor'ed.gif"
                            alt="Remove Animation"
                            width="40"
                            height="40">

                    </button>
                </form>

            </div>

            <form class="activity-form">
                <input type="hidden" name="course_index" value="<?= $idx ?>">
                <p>Units: <input class="units-input" type="number" name="units" value="<?= $course['units'] ?>"></p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>Score</th>
                                <th>Max</th>
                                <th>Weight</th>
                                <th>Raw %</th>
                                <th>Weighted %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $acts = $course['activities'] ?: [['name' => '', 'score' => 0, 'max' => 0, 'weight' => 0]];
                            foreach ($acts as $a):
                            ?>
                                <tr>
                                    <td><input name="activity[]" value="<?= htmlspecialchars($a['name']) ?>"></td>
                                    <td><input name="score[]" type="number" value="<?= $a['score'] ?>"></td>
                                    <td><input name="max[]" type="number" value="<?= $a['max'] ?>"></td>
                                    <td><input name="weight[]" type="number" value="<?= $a['weight'] ?>"></td>
                                    <td>0%</td>
                                    <td>0%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="weight-bar">
                    <div class="weight-fill"></div>
                </div>
                <p>Total Weight: <strong class="weight-total">0%</strong> <span class="weight-warning">must be 100%</span></p>
                <p class="final-grade">Final Grade: 0%</p>

                <button type="button" class="add-activity-btn">+ Add Activity</button>
            </form>
        </div>
    <?php endforeach; ?>

    <div id="gwa-footer">

        <span id="gwa-text">GWA: —</span>
        <img id="gwa-reaction" src="assets/picture/kirbyGwaReactions/kirbyReaction-1.png" alt="Reaction">
    </div>


    <script>
        const root = document.documentElement;
        const toggle = document.getElementById("themeToggle");


        function convertToUPGrade(p) {
            return p >= 97 ? 1 : p >= 94.25 ? 1.25 : p >= 91.5 ? 1.5 : p >= 88.75 ? 1.75 : p >= 86 ? 2 : p >= 83.25 ? 2.25 : p >= 80.5 ? 2.5 : p >= 77.75 ? 2.75 : p >= 75 ? 3 : 4;
        }

        function updateCourse(form) {
            let totalWeight = 0,
                final = 0;
            form.querySelectorAll("tbody tr").forEach(row => {
                const s = +row.children[1].firstChild.value || 0;
                const m = +row.children[2].firstChild.value || 0;
                const w = +row.children[3].firstChild.value || 0;
                const raw = m ? (s / m) * 100 : 0;
                const weighted = raw * w / 100;
                row.children[4].textContent = raw.toFixed(2) + "%";
                row.children[5].textContent = weighted.toFixed(2) + "%";
                totalWeight += w;
                final += weighted;
            });
            form.querySelector(".weight-total").textContent = totalWeight + "%";
            form.querySelector(".weight-warning").style.display = totalWeight === 100 ? "none" : "inline";
            const bar = form.querySelector(".weight-fill");
            bar.style.width = Math.min(totalWeight, 100) + "%";
            bar.style.background = totalWeight === 100 ? "#22c55e" : "#f59e0b";
            form.querySelector(".final-grade").textContent = `Final Grade: ${final.toFixed(2)}% → UP Grade: ${convertToUPGrade(final)}`;
            autoSave(form);
            updateGWA();
        }

        function updateReaction(gwa) {
            const img = document.getElementById("gwa-reaction");
            let r = 1;

            if (gwa <= 1.49) r = 10;
            else if (gwa <= 1.99) r = 9;
            else if (gwa <= 2.24) r = 8;
            else if (gwa <= 2.49) r = 7;
            else if (gwa <= 2.74) r = 6;
            else if (gwa <= 2.99) r = 5;
            else if (gwa <= 3.24) r = 4;
            else if (gwa <= 3.49) r = 3;
            else if (gwa <= 3.74) r = 2;

            img.src = `assets/picture/kirbyGwaReactions/kirbyReaction-${r}.png`;
        }


        function updateGWA() {
            let sum = 0,
                units = 0;

            document.querySelectorAll(".activity-form").forEach(f => {
                const u = +f.querySelector('[name="units"]').value || 3;
                let fp = 0;

                f.querySelectorAll("tbody tr").forEach(r => {
                    fp += +r.children[5].textContent.replace('%', '') || 0;
                });

                sum += convertToUPGrade(fp) * u;
                units += u;
            });

            if (!units) {
                document.getElementById("gwa-text").textContent = "GWA: —";
                return;
            }

            const gwa = (sum / units).toFixed(2);
            document.getElementById("gwa-text").textContent =
                "General Weighted Average (GWA): " + gwa;

            updateReaction(parseFloat(gwa));
        }


        function autoSave(form) {
            const fd = new FormData(form);
            fd.append("auto_save", 1);
            fetch("", {
                method: "POST",
                body: fd
            });
        }


        document.addEventListener("input", e => {
            const row = e.target.closest("tr");
            if (!row) return;

            const scoreInput = row.querySelector('input[name="score[]"]');
            const maxInput = row.querySelector('input[name="max[]"]');

            if (e.target === scoreInput || e.target === maxInput) {
                const score = parseFloat(scoreInput.value) || 0;
                const max = parseFloat(maxInput.value) || 0;

                if (score > max) scoreInput.value = max;
            }


            const form = e.target.closest(".activity-form");
            if (form) updateCourse(form);
        });


        document.addEventListener("input", e => {
            if (e.target.closest(".activity-form")) updateCourse(e.target.closest(".activity-form"));
        });

        document.addEventListener("click", e => {
            if (!e.target.classList.contains("add-activity-btn")) return;
            e.target.closest("form").querySelector("tbody").insertAdjacentHTML("beforeend", `
        <tr>
            <td><input name="activity[]"></td>
            <td><input name="score[]" type="number"></td>
            <td><input name="max[]" type="number"></td>
            <td><input name="weight[]" type="number"></td>
            <td>0%</td><td>0%</td>
        </tr>
    `);
        });

        document.querySelectorAll(".activity-form").forEach(updateCourse);
        document.querySelectorAll(".kirby-hover").forEach(img => {
            const btn = img.closest("button");

            btn.addEventListener("mouseenter", () => {
                img.src = img.dataset.gif;
            });

            btn.addEventListener("mouseleave", () => {
                img.src = img.dataset.static;
            });
        });
    </script>

</body>


</html>