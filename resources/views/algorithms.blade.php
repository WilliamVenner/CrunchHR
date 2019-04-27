<?php
    $unsorted = [];
    for ($i = 0; $i <= 20; $i++) {
        $unsorted[] = rand(0,100);
    }
    
    $bubble_sorted = \App\Helpers::BubbleSort($unsorted);
    $merge_sorted  = \App\Helpers::MergeSort($unsorted);
    
    $find = $unsorted[array_rand($unsorted)];
    $binary_search = \App\Helpers::BinarySearch($merge_sorted, $find);
    $linear_search = \App\Helpers::LinearSearch($merge_sorted, $find);
?>

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 14px;
    }
</style>

The exam board states that markable technical skills include bubble sorting, merge sorting, binary tree searching and linear searching.<br>
Due to the nature of this coursework, as proof of concept, I have written these algorithms and created this page to prove that as a student, it is within my ability to write and use these algorithms.<br>
I have not used these algorithms in the coursework itself because all searching and sorting is done by the database, and I have not found a reason to use these algorithms due to this.<br>
Furthermore, using these algorithms in place of the databases' own would be very inefficient. For example, to search for a row in a table using my binary search algorithm, every single row in the table would have to be retrieved beforehand, which is a waste of resources.<br><br>

In the tables for this coursework, I have created indexes on columns where appropriate - all of these indexes internally use a binary tree to make SQL statements faster. (This is a feature of MySQL and other database software)<br><br>

<b>Refreshing the page randomizes the data.</b><br>

<pre style="font-family:monospace">source: resources/views/algorithms.blade.php

<u>Sorting Algorithms</u>
unsorted      = {!! json_encode($unsorted) !!}

bubble_sorted = {!! json_encode($bubble_sorted) !!}
                source: app/Helpers.php line 69

merge_sorted  = {!! json_encode($merge_sorted) !!}
                source: app/Helpers.php line 27

<u>Search Algorithms</u>
data          = {!! json_encode($merge_sorted) !!}
find          = {!! $find !!}

binary_search = index #{!! $binary_search !!}
                source: app/Helpers.php line 7

linear_search = index #{!! $linear_search !!}
                source: app/Helpers.php line 88</pre>