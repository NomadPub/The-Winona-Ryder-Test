<?php
/*
Plugin Name: The Winona Ryder Test
Description: A fun test to determine if you're Gen X or Millennial based on your Winona Ryder associations.
Version: 1.1
Author: Your Name
*/

function winona_ryder_test_shortcode() {
    // Load the answer files
    $millennial_answers = file(plugin_dir_path(__FILE__) . '/Millennial-Answers.txt', FILE_IGNORE_NEW_LINES);
    $genx_answers = file(plugin_dir_path(__FILE__) . '/GenX-Answers.txt', FILE_IGNORE_NEW_LINES);
    
    // Handle form submission
    $result = '';
    if (isset($_POST['winona_answer'])) {
        $user_answer = trim($_POST['winona_answer']);
        
        if (empty($user_answer)) {
            $result = '<div class="winona-result">You\'re probably a Gen Z or something</div>';
        } else {
            // Fuzzy match threshold (lower = stricter matching)
            $threshold = 3;
            
            // Check Millennial answers
            $millennial_match = false;
            $best_millennial_score = PHP_INT_MAX;
            $best_millennial_match = '';
            
            foreach ($millennial_answers as $answer) {
                $score = levenshtein(strtolower($user_answer), strtolower($answer));
                if ($score < $best_millennial_score) {
                    $best_millennial_score = $score;
                    $best_millennial_match = $answer;
                }
                if ($score <= $threshold) {
                    $millennial_match = true;
                    break;
                }
            }
            
            // Check Gen X answers
            $genx_match = false;
            $best_genx_score = PHP_INT_MAX;
            $best_genx_match = '';
            
            foreach ($genx_answers as $answer) {
                $score = levenshtein(strtolower($user_answer), strtolower($answer));
                if ($score < $best_genx_score) {
                    $best_genx_score = $score;
                    $best_genx_match = $answer;
                }
                if ($score <= $threshold) {
                    $genx_match = true;
                    break;
                }
            }
            
            // Determine result
            if ($millennial_match && !$genx_match) {
                $result = '<div class="winona-result">Millennial answer.</div>';
            } elseif ($genx_match && !$millennial_match) {
                $result = '<div class="winona-result">Gen X answer.</div>';
            } elseif ($millennial_match && $genx_match) {
                // If both match, use the better score
                if ($best_millennial_score < $best_genx_score) {
                    $result = '<div class="winona-result">Millennial answer.</div>';
                } else {
                    $result = '<div class="winona-result">Gen X answer.</div>';
                }
            } else {
                // No close matches found, show the closest from either category
                if ($best_millennial_score < $best_genx_score && $best_millennial_score < 10) {
                    $result = '<div class="winona-result">Did you mean "'.$best_millennial_match.'"? Millennial answer.</div>';
                } elseif ($best_genx_score < 10) {
                    $result = '<div class="winona-result">Did you mean "'.$best_genx_match.'"? Gen X answer.</div>';
                } else {
                    $result = '<div class="winona-result">You\'re probably a Gen Z or something</div>';
                }
            }
        }
    }
    
    // Output the form and result
    $output = '
    <div class="winona-ryder-test">
        <h3>Are you Gen X or a Millennial? What\'s the first thing that comes to mind when you think of Winona Ryder?</h3>
        <form method="post" action="">
            <input type="text" name="winona_answer" placeholder="Enter your answer..." />
            <input type="submit" value="Submit" />
        </form>
        ' . $result . '
    </div>
    <style>
        .winona-ryder-test {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }
        .winona-ryder-test input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
        }
        .winona-ryder-test input[type="submit"] {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .winona-result {
            margin-top: 20px;
            padding: 10px;
            font-weight: bold;
            font-size: 1.2em;
        }
    </style>
    ';
    
    return $output;
}

add_shortcode('winona_ryder_test', 'winona_ryder_test_shortcode');
?>