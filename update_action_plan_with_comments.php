<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$actionPlan = App\Models\ActionPlan::where('store_visit_id', 2)->first();

if ($actionPlan && $actionPlan->storeVisit) {
    $visit = $actionPlan->storeVisit;
    
    // Update Issue field with general comments
    $issueDescription = $actionPlan->item . ' - Issue identified during visit';
    
    $generalCommentsParts = [];
    if (!empty($visit->general_comments)) {
        $generalCommentsParts[] = 'General Comments: ' . $visit->general_comments;
    }
    if (!empty($visit->what_did_you_see)) {
        $generalCommentsParts[] = 'What Did You See: ' . $visit->what_did_you_see;
    }
    if (!empty($visit->why_had_issue)) {
        $generalCommentsParts[] = 'Why We Had Issue: ' . $visit->why_had_issue;
    }
    if (!empty($visit->how_to_improve)) {
        $generalCommentsParts[] = 'How To Improve: ' . $visit->how_to_improve;
    }
    if (!empty($visit->who_when_responsible)) {
        $generalCommentsParts[] = 'Who/When Responsible: ' . $visit->who_when_responsible;
    }
    
    if (!empty($generalCommentsParts)) {
        $issueDescription .= ' | ' . implode(' | ', $generalCommentsParts);
    }
    
    // Build comprehensive notes with all comments
    $notesContent = 'Action required based on store visit findings';
    if (!empty($generalCommentsParts)) {
        $notesContent .= "\n\n" . implode("\n", $generalCommentsParts);
    }
    
    // Update action plan
    $actionPlan->issue = $issueDescription;
    $actionPlan->notes = $notesContent;
    $actionPlan->what = !empty($visit->what_did_you_see) ? $visit->what_did_you_see : $actionPlan->what;
    $actionPlan->why = !empty($visit->why_had_issue) ? $visit->why_had_issue : $actionPlan->why;
    $actionPlan->who = !empty($visit->who_when_responsible) ? $visit->who_when_responsible : $actionPlan->who;
    $actionPlan->how = !empty($visit->how_to_improve) ? $visit->how_to_improve : $actionPlan->how;
    $actionPlan->save();
    
    echo "✅ Action Plan Updated!\n\n";
    echo "Issue: {$actionPlan->issue}\n\n";
    echo "Notes: {$actionPlan->notes}\n";
} else {
    echo "Action plan or store visit not found!\n";
}
