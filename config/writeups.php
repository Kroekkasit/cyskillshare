<?php

declare(strict_types=1);

return [
    'words_per_minute' => 200,
    'autosave_min_interval_seconds' => 8,
    'max_content_length' => 200000,
    'max_tags' => 12,
    'max_skills' => 8,

    'reviewer_roles' => ['instructor', 'mentor', 'admin', 'moderator'],
    'manager_roles' => ['instructor', 'admin', 'moderator'],

    'images' => [
        'max_bytes' => 5 * 1024 * 1024,
        'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
    ],

    'templates' => [
        'ctf_writeup' => "## Challenge\n\n**Difficulty:**\n**Category:**\n\n## 1. Reconnaissance\n\n## 2. Initial Analysis\n\n## 3. Vulnerability\n\n## 4. Exploitation\n\n## 5. Solution\n\n## 6. Lessons Learned\n",
        'malware_analysis' => "## 1. Sample Information\n\n## 2. Static Analysis\n\n## 3. Behavioral Analysis\n\n## 4. Network Indicators\n\n## 5. Persistence\n\n## 6. Capabilities\n\n## 7. IOCs\n\n## 8. Conclusion\n",
        'digital_forensics' => "## 1. Case Description\n\n## 2. Evidence\n\n## 3. Acquisition\n\n## 4. Analysis\n\n## 5. Findings\n\n## 6. Timeline\n\n## 7. Conclusion\n",
        'security_research' => "## 1. Background\n\n## 2. Threat / Problem\n\n## 3. Methodology\n\n## 4. Analysis\n\n## 5. Findings\n\n## 6. Impact\n\n## 7. Mitigation\n\n## 8. References\n",
    ],
];
