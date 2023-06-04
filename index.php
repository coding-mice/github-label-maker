<?php
/**
 * This script fixes github project labels
 * Updates the existing (invalid,question)
 * Adds normal flow labels
 */

require 'config.php';

function callGithubUrl($url){
    $headers = getHeaders();

    $url = str_replace('https://api.github.com','',$url);

    $ch = curl_init('https://api.github.com'.$url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    curl_close($ch);

    $output = json_decode($server_output);

    if(is_object($output) && property_exists($output,'message') && $output->message==="Bad credentials"){
      throw new \Exception($output->message);
    }

    return $output;
}

function createLabel($url,$array){

    $data = json_encode($array);

    $headers = getHeaders();

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => $headers
    ]);

    curl_exec($curl);
    curl_close($curl);
}

function updateLabel($url,$array){

    $data = json_encode($array);

    $headers = getHeaders();

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CUSTOMREQUEST => 'PATCH',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => $headers
    ]);
    curl_exec($curl);
    curl_close($curl);
}

function deleteLabel($url){

    $headers = getHeaders();

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_HTTPHEADER => $headers
    ]);
    curl_exec($curl);
    curl_close($curl);
}

function getOrgRepos($page)
{
    global $orgName;
    return callGithubUrl('/orgs/'.$orgName.'/repos?page='.$page);
}

$repoLabels = [];

$desiredLabelColors = [
    'bug' => '#d73a4a',
    'enhancement' => '#a2eeef',
    'duplicate'=>'#cfd3d7',
    'good first issue' => '#7057ff',
    'help wanted' => '#008672',
    'invalid' => '#e4e669',
    'question' => '#d876e3',
    'wontfix' => '#ffffff',
];

$page = 1;
while($repos = getOrgRepos($page)) {

    foreach ($repos as $repo) {

        if($repo->archived){
            continue;
        }

        $labels = callGithubUrl($repo->url . '/labels');
        foreach ($labels as $label) {
            if (!in_array($label->name, ['bug', 'enhancement', 'wontfix', 'good first issue', 'duplicate', 'help wanted'])) {
                $repoLabels[$repo->name]['labels'][] = $label->name;
            }
        }

        if (in_array('invalid', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Invalid',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            updateLabel($repo->url . '/labels/invalid', $array);
        }

        if (in_array('question', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Required Information',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            updateLabel($repo->url . '/labels/question', $array);
        }

        if (!in_array('Estimate Required', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Estimate Required',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('High Priority', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'High Priority',
                'description' => '',
                'color' => 'ff0000'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('Low Priority', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Low Priority',
                'description' => '',
                'color' => 'b9def0'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('Estimate Done', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Estimate Done',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('Queued', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Queued',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('Test On Dev', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Test On Dev',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('Dev Accepted', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Dev Accepted',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('Test On Live', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Test On Live',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('Live Accepted', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Live Accepted',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('Fixed', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Fixed',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('CR Needed', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'CR Needed',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (in_array('Code Review Needed', $repoLabels[$repo->name]['labels'])) {
            deleteLabel($repo->url . '/labels/'.urlencode('Code Review Needed'));
        }

        if (!in_array('CR Done', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'CR Done',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('Test Needed', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Test Needed',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('Test Created', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Test Created',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        if (!in_array('regression', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'regression',
                'description' => '',
                'color' => 'ff0000'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        echo PHP_EOL.'Done creating labels for ' . $repo->name;
    }
    $page++;
}

function getHeaders(){
    global $personalToken,$userName;
    return [
        'Authorization: token '.$personalToken,
        'Accept: application/vnd.github.v3+json',
        'User-Agent: '.$userName
    ];
}


