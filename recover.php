<?php
$file = 'C:/Users/HYPE AMD/.gemini/antigravity/brain/bed31ab0-d8a0-47b1-a603-92fec16b40c3/.system_generated/logs/transcript_full.jsonl';
$lines = file($file);

$bestContent = '';
$count = 1;
foreach ($lines as $line) {
    $data = json_decode($line, true);
    if (!$data) continue;
    
    if (isset($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $call) {
            if ($call['name'] === 'write_to_file' || $call['name'] === 'replace_file_content' || $call['name'] === 'multi_replace_file_content') {
                $args = $call['args'];
                $targetFile = $args['TargetFile'] ?? '';
                if (strpos($targetFile, 'finance-hub.blade.php') !== false) {
                    $bestContent .= "\n\n=== MATCH $count ===\n";
                    if (isset($args['CodeContent'])) {
                        $bestContent .= "FULL:\n" . substr($args['CodeContent'], 0, 500) . "...\n";
                    } else {
                        $bestContent .= "PATCH:\n" . json_encode($args) . "\n";
                    }
                    $count++;
                }
            }
        }
    }
}

file_put_contents('recover.txt', $bestContent);
echo "Recovered $count matches to recover.txt";
