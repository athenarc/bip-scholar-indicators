<?php

// Include the ScholarIndicators class
require_once 'ScholarIndicators.php';

use schatzopoulos\scholarindicators\ScholarIndicators;

// Sample data
$impact_fields = [
    'popularity' => 'popularity_score',
    'influence' => 'influence_score',
    'impulse' => 'impulse_score',
];

$impact_classes = ['high', 'medium', 'low'];

$work_types = [
    'papers' => 'paper',
    'dataset' => 'dataset',
    'software' => 'software',
    'other' => 'other',
];

$papers = [
    [
        'citation_count' => 15,
        'type' => 'paper',
        'popularity_score' => 10,
        'influence_score' => 5,
        'impulse_score' => 3,
        'pop_class' => 'high',
        'inf_class' => 'medium',
        'is_oa' => 1,
        'year' => 2020,
    ],
    [
        'citation_count' => 25,
        'type' => 'dataset',
        'popularity_score' => 8,
        'influence_score' => 7,
        'impulse_score' => 4,
        'pop_class' => 'medium',
        'inf_class' => 'low',
        'is_oa' => 0,
        'year' => 2018,
    ],
    [
        'citation_count' => 5,
        'type' => 'software',
        'popularity_score' => 3,
        'influence_score' => 2,
        'impulse_score' => 1,
        'pop_class' => 'high',
        'inf_class' => 'low',
        'is_oa' => 1,
        'year' => 2019,
    ],
];

// Sample data for inactivity periods
$rag_data = [
    ['start_date' => '2015-01-01', 'end_date' => '2016-12-31'],
    ['start_date' => '2018-01-01', 'end_date' => '2018-12-31'],
];

// Initialize the ScholarIndicators class
$indicators = new ScholarIndicators($impact_fields, $impact_classes, $work_types, $papers);

// Use individual methods
echo "=== Testing Individual Methods ===\n";

echo "Work Types Count:\n";
print_r($indicators->work_types_num());

echo "\nTotal Citations: " . $indicators->citations_num() . "\n";

echo "h-Index: " . $indicators->h_index() . "\n";

echo "i10-Index: " . $indicators->i10_index() . "\n";

echo "\nPopularity Sum:\n";
print_r($indicators->popularity_sum());

echo "\nInfluence Sum:\n";
print_r($indicators->influence_sum());

echo "\nImpulse Sum: " . $indicators->impulse_sum() . "\n";

echo "\nOpen Access Papers Percentage:\n";
print_r($indicators->open_papers_percentage());

echo "\nMinimum Publication Year: " . $indicators->get_paper_min_year() . "\n";

echo "Academic Age: " . $indicators->get_academic_age($indicators->get_paper_min_year()) . "\n";

echo "Responsible Academic Age:\n";
print_r(ScholarIndicators::get_responsible_academic_age(
    $indicators->get_academic_age($indicators->get_paper_min_year()),
    $rag_data,
    $indicators->get_paper_min_year()
));

echo "\nPopular Works Count (from all works):\n";
print_r($indicators->popular_works_count($papers));

echo "\nInfluential Works Count (from all works):\n";
print_r($indicators->influential_works_count($papers));

echo "\n=== Testing Compute Method ===\n";

// Alternatively, use the compute() method to compute all indicators at once
$all_metrics = $indicators->compute($rag_data);
print_r($all_metrics);

?>
