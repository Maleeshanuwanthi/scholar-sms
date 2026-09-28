<?php
// Returns an associative array of subject groups for the given grade string.
// Each key is a group label (shown as an <optgroup>), each value is a list of subjects.
function get_subject_groups($grade) {
    $num = 0;
    if (preg_match('/([0-9]+)/', $grade, $m)) {
        $num = intval($m[1]);
    }

    // Grades 6-9: fixed core subject list
    if ($num >= 6 && $num <= 9) {
        return [
            "Subjects" => [
                "English", "Mathematics", "Science", "Sinhala", "Buddhism",
                "Citizenship Education", "Tamil", "Geography", "P.T.S", "History"
            ]
        ];
    }

    // Grades 10-11 (O/L): Core subjects + 3 baskets
    if ($num >= 10 && $num <= 11) {
        return [
            "Core Subjects" => [
                "English", "Mathematics", "Science", "Sinhala", "Buddhism", "Tamil", "History"
            ],
            "Basket 1" => [
                "Geography", "Citizenship Education"
            ],
            "Basket 2 - Aesthetic" => [
                "Art", "Music", "Dancing", "Literature"
            ],
            "Basket 3 - Technology" => [
                "ICT", "Health"
            ],
        ];
    }

    // Grades 12-13 (A/L): subjects grouped by stream
    if ($num >= 12 && $num <= 13) {
        return [
            "Bio / Maths Stream" => [
                "Biology", "Mathematics", "Chemistry", "Physics", "Agriculture"
            ],
            "Arts Stream" => [
                "Sinhala", "History", "Geography", "Economics", "Political Science", "Logic & Scientific Method"
            ],
            "Commerce Stream" => [
                "Business Studies", "Accounting", "Economics", "Business Statistics"
            ],
            "Technology Stream" => [
                "Science for Technology", "Engineering Technology", "Bio-System Technology", "ICT"
            ],
        ];
    }

    // Fallback for anything else (e.g. Grade 1-5 primary)
    return [
        "Subjects" => ["English", "Mathematics", "Science", "Sinhala", "Environment"]
    ];
}