<?php

namespace App\Helpers;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
class PDFSanitizer
{

    public  function sanitize($file)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getRealPath());

        //todo integrate with a real-time scanning service or ai model
        $maliciousKeywords = [
            'eval(',                  // PHP eval function, often used for code injection
            'phpinfo()',              // Often used in exploit scripts to display PHP information
            'shell_exec',             // Allows command execution
            'exec(',                  // Similar to shell_exec, used for executing commands
            'system(',                // Another function that allows command execution
            'passthru(',              // Another function to run commands
            'curl_exec',              // Allows making system requests
            'file_get_contents',      // Sometimes used for malicious URL fetching
            'fopen',                  // File operations that could be exploited
            '$_GET',                  // Global variable often used in injections
            '$_POST',                 // Another global variable often used in exploits
            '$_REQUEST',              // A superglobal that can be manipulated in attacks
            'eval(base64_decode',     // Obfuscated PHP code
            'xss',                    // Cross-site scripting
            'cmd.exe',                // Windows command execution attempt
            'rm -rf',                 // Dangerous command in Unix-based systems
            'MALICIOUS',              // Generic placeholder for any custom malicious term
            'virus',                  // General term for viruses or malware
            'trojan',                 // Type of malware
            'malware',                // Malware
            'exploit',                // Common term in exploits
            'phishing',               // Commonly associated with malicious activities
            'hack',                   // Related to hacking
            'inject',                 // Often related to SQL or code injections
        ];

        // Verify content for unexpected characters or validate content
        $text = $pdf->getText();
        if (Str::contains($text, $maliciousKeywords)) {
            return response()->json(['message' => 'File contains invalid content.'], 400);
        }
        return response()->json(['message' => 'File is clean.'], 200);
    }

}
