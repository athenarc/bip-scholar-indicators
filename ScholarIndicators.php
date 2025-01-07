<?php

# Please see details on how to update the extension here:
# https://www.yiiframework.com/wiki/846/yii2-how-to-createdevelop-a-new-extension-using-composer-locally-without-version-control-or-git

namespace schatzopoulos\scholarindicators;

use Yii;

/**
 * ScholarIndicators class computes various researcher-level indicators.
 */
class ScholarIndicators
{
    public $citations; // Array of citation counts for each paper
    public $papers; // Array of papers with metadata
    private $impact_fields; // Mapping of impact fields (popularity, influence, impulse, etc.)
    private $impact_classes; // Classes representing different impact levels
    private $work_types; // Mapping of work types (papers, datasets, software, etc.)

    /**
     * Constructor to initialize the ScholarIndicators class.
     * 
     * @param array $impact_fields Mapping of impact field names.
     * @param array $impact_classes List of impact classes.
     * @param array $work_types Mapping of work types.
     * @param array $papers Array of papers with metadata.
     */
    public function __construct($impact_fields, $impact_classes, $work_types, $papers)
    {
        $this->citations = array_column($papers, 'citation_count');
        $this->papers = $papers;
        $this->impact_fields = $impact_fields;
        $this->impact_classes = $impact_classes;
        $this->work_types = $work_types;
    }

    /**
     * Computes the number of works by type (papers, datasets, software, other).
     * 
     * @return array Associative array with counts for each type.
     */
    public function work_types_num()
    {
        $types = [
            'papers' => 0,
            'datasets' => 0,
            'software' => 0,
            'other' => 0,
        ];

        foreach ($this->papers as $paper) {
            if ($paper['type'] == $this->work_types['dataset']) {
                $types['datasets']++;
            } elseif ($paper['type'] == $this->work_types['software']) {
                $types['software']++;
            } elseif ($paper['type'] == $this->work_types['other']) {
                $types['other']++;
            } else {
                $types['papers']++;
            }
        }

        return $types;
    }

    /**
     * Calculates the total number of citations.
     * 
     * @return int Total citations.
     */
    public function citations_num()
    {
        return array_sum($this->citations);
    }

    /**
     * Computes the h-index of the researcher based on citations.
     * 
     * @return int h-index value.
     */
    public function h_index()
    {
        if (array_sum($this->citations) == 0) {
            return 0;
        }

        // Sort citations in descending order
        rsort($this->citations, SORT_NUMERIC);

        $arr = range(1, count($this->citations));

        // h-index is the maximum value of the element-wise minimum of the two arrays
        return max(array_map(function ($a, $b) {
            return ($a < $b) ? $a : $b;
        }, $this->citations, $arr));
    }

    /**
     * Calculates the i10-index (number of papers with at least 10 citations).
     * 
     * @return int i10-index value.
     */
    public function i10_index()
    {
        return array_reduce($this->citations, function ($ret, $val) {
            return $ret += $val >= 10;
        });
    }

    /**
     * Counts the number of popular works (those not in the last impact class).
     * 
     * @param array $all_works Array of all works with metadata.
     * @return int Number of popular works.
     */
    public function popular_works_count($all_works)
    {
        $pop_class_field = 'pop_class';
        $last_impact_class = end($this->impact_classes);

        return array_reduce(
            array_filter(array_column($all_works, $pop_class_field)),
            function ($ret, $val) use ($last_impact_class) {
                return $ret += $val !== $last_impact_class;
            }
        );
    }

    /**
     * Counts the number of influential works (those not in the last impact class).
     * 
     * @param array $all_works Array of all works with metadata.
     * @return int Number of influential works.
     */
    public function influential_works_count($all_works)
    {
        $inf_class_field = 'inf_class';
        $last_impact_class = end($this->impact_classes);

        return array_reduce(
            array_filter(array_column($all_works, $inf_class_field)),
            function ($ret, $val) use ($last_impact_class) {
                return $ret += $val !== $last_impact_class;
            }
        );
    }

    /**
     * Calculates the sum of popularity scores.
     * 
     * @return array Popularity sum in scientific notation.
     */
    public function popularity_sum()
    {
        $pop_field = $this->impact_fields['popularity'];
        $pop_value = sprintf('%.2e', array_sum(array_column($this->papers, $pop_field)));
        list($number, $exponent) = explode('e', $pop_value);
        return [
            'number' => $number,
            'exponent' => 'E' . $exponent,
        ];
    }

    /**
     * Calculates the sum of influence scores.
     * 
     * @return array Influence sum in scientific notation.
     */
    public function influence_sum()
    {
        $inf_field = $this->impact_fields['influence'];
        $inf_value = sprintf('%.2e', array_sum(array_column($this->papers, $inf_field)));
        list($number, $exponent) = explode('e', $inf_value);
        return [
            'number' => $number,
            'exponent' => 'E' . $exponent,
        ];
    }

    /**
     * Calculates the sum of impulse scores.
     * 
     * @return int Impulse sum.
     */
    public function impulse_sum()
    {
        $imp_field = $this->impact_fields['impulse'];
        return array_sum(array_column($this->papers, $imp_field));
    }

    /**
     * Computes the percentage of open access papers.
     * 
     * @return array Open access statistics.
     */
    public function open_papers_percentage()
    {
        $open_papers_array = array_filter($this->papers, function ($item) {
            return $item['is_oa'] == 1;
        });

        $open_papers = count($open_papers_array);

        $closed_papers = count(array_filter($this->papers, function ($item) {
            return $item['is_oa'] === '0';
        }));

        $known_papers = $open_papers + $closed_papers;

        $open_percentage = ($known_papers == 0) ? "" : round(100 * ($open_papers / $known_papers), 0);

        $influential_open_papers = $this->influential_works_count($open_papers_array) ?? 0;
        $popular_open_papers = $this->popular_works_count($open_papers_array) ?? 0;

        return [
            'open_percentage' => $open_percentage,
            'known_papers' => $known_papers,
            'open_papers' => $open_papers,
            'influential_open_papers' => $influential_open_papers,
            'popular_open_papers' => $popular_open_papers,
        ];
    }

    /**
     * Finds the minimum publication year among papers.
     * 
     * @return int|null Minimum year or null if no valid year is found.
     */
    public function get_paper_min_year()
    {
        $years = array_column($this->papers, 'year');

        $years_clean = array_filter($years, function ($value) {
            return isset($value);
        });

        if (empty($years_clean)) {
            return null;
        }

        foreach ($years_clean as $key => $value) {
            $years_clean[$key] = ($value > date('Y')) ? date('Y') : $value;
        }

        return (empty($years_clean)) ? null : min($years_clean);
    }

    /**
     * Calculates the academic age of the researcher (years since first publication).
     * 
     * @param int|null $min_year The earliest year of publication.
     * @return int|null Academic age or null if no valid year is found.
     */
    public function get_academic_age($min_year)
    {
        return (empty($min_year)) ? null : (date('Y') - $min_year);
    }

    /**
     * Adjusts the academic age based on periods of inactivity (responsible academic age).
     * 
     * @param int|null $academic_age The academic age of the researcher.
     * @param array $rag_data Array of inactivity periods with start and end dates.
     * @param int|null $min_year The earliest year of publication.
     * @return float|null Adjusted academic age or null if inputs are invalid.
     */
    public static function get_responsible_academic_age($academic_age, $rag_data, $min_year)
    {
        if (empty($min_year) || empty($academic_age)) {
            return null;
        }

        if (empty($rag_data)) {
            return $academic_age;
        }

        // Combine overlapping inactivity date ranges
        $rag_data_ranges = self::mergeDateRanges($rag_data);

        if (empty($rag_data_ranges)) {
            return $academic_age;
        }

        $date_now = new \DateTime(date('Y-m-d'));
        $min_date = $min_year . '-01-01';
        $date_paper_min = new \DateTime($min_date);

        $total_interval = $date_paper_min->diff($date_now)->format('%r%a days');
        $total_removed_interval = 0;

        foreach ($rag_data_ranges as $rag_row) {
            $interval = 0;
            $start_date = new \Datetime($rag_row['start_date']);
            $end_date = new \Datetime($rag_row['end_date']);

            if ($end_date > $date_paper_min && $end_date <= $date_now) {
                if ($start_date >= $date_paper_min) {
                    $interval = $start_date->diff($end_date)->format('%a');
                } else {
                    $interval = $date_paper_min->diff($end_date)->format('%a');
                }
            } elseif ($end_date > $date_now && $start_date < $date_now) {
                $interval = $start_date->diff($date_now)->format('%a');
            }
            $total_removed_interval += $interval;
        }

        if (empty($total_interval) || empty($total_removed_interval)) {
            return $academic_age;
        }

        // Compute remaining interval in days and convert to years
        $total_remaining_interval = ($academic_age * 365) - $total_removed_interval;

        $total_remaining_interval = ($total_remaining_interval <= 0)
            ? 0
            : Yii::$app->formatter->asDecimal(($total_remaining_interval / 365), 2);

        return $total_remaining_interval;
    }

    /**
     * Extracts and keeps only the start and end date keys from the inactivity periods.
     * 
     * @param array $rag_data Array of inactivity periods with start and end dates.
     * @return array Simplified array containing only start_date and end_date keys.
     */
    public static function keepOnlyDateRanges($rag_data)
    {
        $ranges = [];
        foreach ($rag_data as $rag_row) {
            $ranges[] = [
                'start_date' => $rag_row['start_date'],
                'end_date' => $rag_row['end_date'],
            ];
        }
        return $ranges;
    }

    /**
     * Merges overlapping or adjacent date ranges into a consolidated list.
     * 
     * @param array $rag_data Array of inactivity periods with start and end dates.
     * @return array Merged date ranges.
     */
    public static function mergeDateRanges($rag_data)
    {
        $ranges = self::keepOnlyDateRanges($rag_data);
        $retVal = [];

        // Sort ranges by start date
        usort($ranges, function ($a, $b) {
            return strcmp($a['start_date'], $b['start_date']);
        });

        $currentRange = [];
        foreach ($ranges as $range) {
            // Skip invalid ranges
            if ($range['start_date'] >= $range['end_date']) {
                continue;
            }

            // Initialize the first range
            if (empty($currentRange)) {
                $currentRange = $range;
                continue;
            }

            // Merge overlapping ranges
            if ($currentRange['end_date'] < $range['start_date']) {
                $retVal[] = $currentRange;
                $currentRange = $range;
            } elseif ($currentRange['end_date'] < $range['end_date']) {
                $currentRange['end_date'] = $range['end_date'];
            }
        }

        if ($currentRange) {
            $retVal[] = $currentRange;
        }

        return $retVal;
    }

    /**
     * Computes all scholarly metrics and returns them in an associative array.
     * 
     * @param array $rag_data Array of inactivity periods with start and end dates.
     * @return array Computed scholarly metrics.
     */
    public function compute($rag_data)
    {
        $work_types_num = $this->work_types_num();
        $citations_num = $this->citations_num();
        $h_index = $this->h_index();
        $i10_index = $this->i10_index();
        $popular_works_count = $this->popular_works_count($this->papers);
        $influential_works_count = $this->influential_works_count($this->papers);
        $popularity = $this->popularity_sum();
        $influence = $this->influence_sum();
        $impulse = $this->impulse_sum();
        $openness = $this->open_papers_percentage();
        $paper_min_year = $this->get_paper_min_year();
        $academic_age = $this->get_academic_age($paper_min_year);

        $responsible_academic_age = self::get_responsible_academic_age($academic_age, $rag_data, $paper_min_year);

        return [
            'work_types_num' => $work_types_num,
            'citations_num' => $citations_num,
            'h_index' => $h_index,
            'i10_index' => $i10_index,
            'popular_works_count' => $popular_works_count,
            'influential_works_count' => $influential_works_count,
            'popularity' => $popularity,
            'influence' => $influence,
            'impulse' => $impulse,
            'openness' => $openness,
            'paper_min_year' => intval($paper_min_year),
            'academic_age' => $academic_age,
            'responsible_academic_age' => floatval($responsible_academic_age),
        ];
    }

}
