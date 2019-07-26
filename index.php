<?php
/**
 * This script fixes github project labels
 * Updates the existing (invalid,question)
 * Adds normal flow labels like Test On Dev
 */

require 'config.php';

function callGithubUrl($url,$method='GET'){
    $headers = getHeaders();

    $url = str_replace('https://api.github.com','',$url);

    $ch = curl_init('https://api.github.com'.$url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    curl_close($ch);
    return json_decode($server_output);
}

function createLabel($url,$array){

    $data = json_encode($array);

    $headers = getHeaders();

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    //var_dump($response);
    curl_close($curl);
}

function updateLabel($url,$array){

    $data = json_encode($array);

    $headers = getHeaders();

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    //var_dump($response);
    curl_close($curl);
}

function getOrgRepos($page)
{
    global $orgName;
    return callGithubUrl('/orgs/'.$orgName.'/repos?page='.$page);
}

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
        } else {
            //var_dump('no invalid label');
        }

        if (in_array('question', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Required Information',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            updateLabel($repo->url . '/labels/question', $array);
        } else {
            //var_dump('no question label in '. $repo->name);
            //var_dump($repoLabels[$repo->name]['labels']);
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

        if (!in_array('Code Review Needed', $repoLabels[$repo->name]['labels'])) {
            $array = [
                'name' => 'Code Review Needed',
                'description' => '',
                'color' => 'e6e6e6'
            ];
            createLabel($repo->url . '/labels', $array);
        }

        var_dump('done creating labels for ' . $repo->name);
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


