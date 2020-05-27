<?php

declare(strict_types=1);

// namespace Helpers;

//------------------------------------------------------------- getRedirectUrl
function getRedirectUrl(string $url): string
{
    $redirect_url = $url;

    $headers = get_headers($url);

    foreach ($headers as $header) {
        if (substr($header, 0, 10) === 'Location: ') {
            $redirect_url = trim(substr($header, 10));
            break;
        }
    }

    return $redirect_url;
}


//------------------------------------------------------------- flattenArray
function flattenArray(array $nested_arrays): void
{
    foreach ($nested_arrays as $key => $value) {
        if (gettype($value) !== 'array') {
            echo ('<pre>' . var_export($key, true) . ' => '
                . var_export($value, true) . '</pre>');
        } else {
            flattenArray($value);
        }
    }
}

//-------------------------------------------------------------- prettyArray
function prettyArray(array $nested_arrays): void
{
    foreach ($nested_arrays as $key => $value) {
        if (gettype($value) !== 'array') {
            echo ('<li class="dump">' . $key . ' : '
                . $value . '</li>');
        } else {
            echo ('<ul class="dump">' . $key);
            prettyArray($value);
            echo ('</ul>');
        }
    }
}

//-------------------------------------------------------------- prettyTable
function prettyTable(array $query_results, string $css_class): void
{
    /* table header */
    $col_names = array_keys($query_results[0]);
    echo '<table class="' . $css_class . '"><thead class="' . $css_class . '">'
        . '<tr class="' . $css_class . '">';

    foreach ($col_names as $col_name) {
        echo '<th class="' . $css_class . '">' . $col_name . '</th>';
    }
    echo '</tr></thead><tbody>';

    /* table body */
    foreach ($query_results as $result) {
        echo '<tr class="' . $css_class . '">';
        foreach ($result as $col => $value) {
            echo '<td class="' . $css_class . '">' . $value . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}
//---------------------------------------------------------------- prettyDump
function prettyDump(array $nested_arrays): void
{
    foreach ($nested_arrays as $key => $value) {
        switch (gettype($value)) {
            case 'array':
                /* ignore same level recursion */
                if ($nested_arrays !== $value) {
                    echo ('<details><summary style="color : tomato;'
                        . 'font-weight : bold;">'
                        . $key . '<span style="color : steelblue;'
                        . 'font-weight : 100;"> ('
                        . count($value) . ')</span>'
                        . '</summary><ul style="font-size: 0.75rem;'
                        . 'background-color: ghostwhite">');
                    prettyDump($value);
                    echo ('</ul></details>');
                }
                break;
            case 'object':
                echo ('<details><summary style="color : tomato;'
                    . 'font-weight : bold;">'
                    . $key . '<span style="color : steelblue;'
                    . 'font-weight : 100;"> ('
                    . gettype($value) . ' : ' . get_class($value) . ')</span>'
                    . '</summary><ul style="font-size: 0.75rem;'
                    . 'background-color: ghostwhite">');
                prettyDump(get_object_vars($value));
                echo ' <details open><summary style="font-weight : bold;'
                    . 'color : plum">(methods)</summary><pre>';
                prettyArray(get_class_methods($value));
                echo '</details></pre>';
                echo '</li></ul></details>';
                break;
            case 'callable':
            case 'iterable':
            case 'resource':
                /* not supported yet */
                break;
            default:
                /* scalar and NULL */
                echo ('<li style="margin-left: 2rem;color: teal;'
                    . 'background-color: white">'
                    . '<span style="color : steelblue;font-weight : bold;">'
                    . $key . '</span> : '
                    . ($value ?? '<span style="font-weight : bold;'
                        . 'color : violet">NULL<span/>')
                    . '</li>');
                break;
        }
    }
}

?>