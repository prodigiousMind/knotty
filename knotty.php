#!/usr/bin/php
<?php

$arr2str = strval(implode(" ", $argv));

# length of supported algorithms in hash_algos
$_supportedLen = count(hash_algos());

# help  function
function generateHelp()
{
    echo "\t\e[1;34;40mknotty.php\e[0m\n\n";
    echo "\t\e[0;31;40mUsage:\e[0m\n";
    echo "\tphp knotty.php plaintext=[PLAINTEXT] [OPTIONS]";
    echo "\n\tphp knotty.php file=[FILENAME] [OPTIONS]";
    echo "\n";
    echo "\n\t\e[1;33;40mREQUIRED ARGUMENTS:\e[0m\n\tEither any of the below\n\n";
    echo "\t1. \e[1;37;40mplaintext=[PLAINTEXT]\e[0m\n\tExample: plaintext=passphrase\n\n";
    echo "\t2. \e[1;37;40mfile=[FILENAME]\e[0m\n\tExample: file=/home/user/file.txt\n\n";
    echo "\n\t\e[1;33;40mOPTIONAL ARGUMENTS:\e[0m\n\n\t** salt1 & salt2 can also be used together **\n";
    echo "\n\t\e[1;37;40msalt1=[SALT]\t(Append salt before the plaintext)\e[0m\n\tExample: plaintext=passphrase salt1=abc123 (abc123passphrase)\n";
    echo "\t\e[1;37;40msalt2=[SALT]\t(Append salt AFTER the plaintext)\e[0m\n\tExample: plaintext=passphrase salt2=abc123 (passphraseabc123)\n\n";
    echo "\t\e[1;37;40mformat=[0 (default), 1, 2, 3]\t(Output Format, if not specified format=0 is used)\e[0m\n\tformat=0\t(Output hashValue)\n\tformat=1\t(Output hashValue:algorithmName)\n\tformat=2\t(Output hashValue:plaintext)\n\tformat=3\t(Output algorithmName:hashValue:plaintext)\n\tExample: plaintext=passphrase format=1\n\t\n";
    echo "\t\e[1;37;40malgo=[all (default), algoName, algoIndex]\t(Algorithm to Compute Hash, if not specified algo=all is used)\e[0m\n\talgo=all\t(Hash will be calculated for all supported algorithms mentioned below)\n\tformat=2\t(MD5 algorithm)\n\tformat=md5\t(MD5 algorithm)\n\tExample: plaintext=passphrase algo=2\n\t\n\n";

    echo "\t\e[0;30;47mSupported Algorithms\e[0m";
    print "\n\tEither Provide the Name or Associated Index Value\n";
    echo "\n\t\e[0;33;40mAlgorithm Index\e[0m" . "\t\t==========\t\t" . "\e[0;36;40mAlgorithm Name\e[0m\n\n";
    echo "\t    all" . "\t\t\t==========\t\t    " . "all\t(All below algorithms will be selected)\n";
    foreach (hash_algos() as $key => $value)
    {
        echo "\t    " . $key . "\t\t\t==========\t\t    " . $value . "\n";
    }
    echo "\n\t\e[1;37;40mEXAMPLE:\e[0m\n\tphp knotty.php plaintext=password salt1=abc123 salt2=xyz format=2 algo=sha1\n\tphp knotty.php plaintext=password salt1=abc123 salt2=xyz format=2 algo=3\n\tphp knotty.php file=/home/user/file.txt algo=all format=1\n\t\n\n";

    exit();
}

# if help is supplied then call help function
if (strpos($arr2str, " help ") !== false || strpos($arr2str, " help") !== false)
{
    generateHelp();
}

# retrieve algorithm name (if none passed, then use all)
function algoName()
{
    global $arr2str, $argv;
    if (strpos($arr2str, "algo") !== false)
    {
        foreach ($argv as $key => $value)
        {
            $tmpA = explode("=", $value);
            if (strtolower($tmpA[0]) == "algo")
            {
                $algo = $tmpA[1];
                break;
            }
        }
    }
    else
    {
        $algo = "all";
    }

    return $algo;
}

# retrieve format of the output (if none passed, 0 is set)
function format()
{
    global $arr2str, $argv;

    if (strpos($arr2str, "format") !== false)
    {
        foreach ($argv as $key => $value)
        {
            $tmpF = explode("=", $value);
            if (strtolower($tmpF[0]) == "format")
            {
                $format = $tmpF[1];
                break;
            }
        }
    }
    else
    {
        $format = "0";
    }

    if (intval($format) < 4 && intval($format) >= 0)
    {
        return $format;
    }
    else
    {
        generateHelp();
    }
}

# check whether plaintext is passed or a file
function plNfi()
{
    global $arr2str, $argv;

    if (strpos($arr2str, "plaintext") !== false xor strpos($arr2str, "file") !== false)
    {
        if (strpos($arr2str, "plaintext") !== false)
        {
            foreach ($argv as $key => $value)
            {
                $tmpPT = explode("=", $value);
                if (strtolower($tmpPT[0]) == "plaintext")
                {
                    $plaintext = $tmpPT[1];
                    break;
                }
            }

            # check if salt1 is passed
            if (strpos($arr2str, "salt1") !== false)
            {
                foreach ($argv as $key => $value)
                {
                    $tmpS1 = explode("=", $value);
                    if (strtolower($tmpS1[0]) == "salt1")
                    {
                        $plaintext = $tmpS1[1] . $plaintext;
                        break;
                    }
                }
            }

            # check if salt2 is passed
            if (strpos($arr2str, "salt2") !== false)
            {
                foreach ($argv as $key => $value)
                {
                    $tmpS2 = explode("=", $value);
                    if (strtolower($tmpS2[0]) == "salt2")
                    {
                        $plaintext = $plaintext . $tmpS2[1];
                        break;
                    }
                }
            }
        }
        else
        {
            foreach ($argv as $key => $value)
            {
                $tmpFI = explode("=", $value);
                if (strtolower($tmpFI[0]) == "file")
                {
                    $plaintext = $tmpFI[1];
                    break;
                }
            }
        }

        # if none is passed, call help function
        
    }
    else
    {
        echo "Either plaintext or file Required!\n";
        generateHelp();
    }
    return $plaintext;
}

# call above function and save the return values in variables
$algo = algoName();
$format = format();
$plaintext = plNfi();

# if algorithm index is pass then compute hash using index
if (is_numeric($algo) && $algo < $_supportedLen)
{

    if (strpos($arr2str, "file") !== false)
    {
        if (strval($format) == "0")
        {
            echo hash_file(hash_algos() [intval($algo) ], $plaintext) . "\n";
        }
        elseif (strval($format) == "1")
        {
            echo hash_file(hash_algos() [intval($algo) ], $plaintext) . ":" . hash_algos() [intval($algo) ] . "\n";
        }
        elseif (strval($format) == "2")
        {
            echo hash_file(hash_algos() [intval($algo) ], $plaintext) . ":" . $plaintext . "\n";
        }
        elseif (strval($format) == "3")
        {
            echo hash_algos() [intval($algo) ] . ":" . hash_file(hash_algos() [intval($algo) ], $plaintext) . ":" . $plaintext . "\n";
        }

        # generate output in appropriate format
        
    }
    else
    {
        if (strval($format) == "0")
        {
            echo hash(hash_algos() [intval($algo) ], $plaintext) . "\n";
        }
        elseif (strval($format) == "1")
        {
            echo hash(hash_algos() [intval($algo) ], $plaintext) . ":" . hash_algos() [intval($algo) ] . "\n";
        }
        elseif (strval($format) == "2")
        {
            echo hash(hash_algos() [intval($algo) ], $plaintext) . ":" . $plaintext . "\n";
        }
        elseif (strval($format) == "3")
        {
            echo hash_algos() [intval($algo) ] . ":" . hash(hash_algos() [intval($algo) ], $plaintext) . ":" . $plaintext . "\n";
        }
    }

    # if algorithm name is passed then check whether the name is valid or not
    
}
else
{
    if (strtolower($algo) == "all")
    {
        foreach (hash_algos() as $key => $value)
        {
            if (strpos($arr2str, "file") !== false)
            {
                if (strval($format) == "0")
                {
                    echo hash_file($value, $plaintext) . "\n";
                }
                elseif (strval($format) == "1")
                {
                    echo hash_file($value, $plaintext) . ":" . $value . "\n";
                }
                elseif (strval($format) == "2")
                {
                    echo hash_file($value, $plaintext) . ":" . $plaintext . "\n";
                }
                elseif (strval($format) == "3")
                {
                    echo $value . ":" . hash_file($value, $plaintext) . ":" . $plaintext . "\n";
                }
            }
            else
            {
                if (strval($format) == "0")
                {
                    echo hash($value, $plaintext) . "\n";
                }
                elseif (strval($format) == "1")
                {
                    echo hash($value, $plaintext) . ":" . $value . "\n";
                }
                elseif (strval($format) == "2")
                {
                    echo hash($value, $plaintext) . ":" . $plaintext . "\n";
                }
                elseif (strval($format) == "3")
                {
                    echo $value . ":" . hash($value, $plaintext) . ":" . $plaintext . "\n";
                }
            }
        }
    }
    elseif (in_array(strtolower($algo) , hash_algos()))
    {
        if (strpos($arr2str, "file") !== false)
        {
            if (strval($format) == "0")
            {
                echo hash_file($algo, $plaintext) . "\n";
            }
            elseif (strval($format) == "1")
            {
                echo hash_file($algo, $plaintext) . ":" . $value . "\n";
            }
            elseif (strval($format) == "2")
            {
                echo hash_file($algo, $plaintext) . ":" . $plaintext . "\n";
            }
            elseif (strval($format) == "3")
            {
                echo $value . ":" . hash_file($algo, $plaintext) . ":" . $plaintext . "\n";
            }
        }
        else
        {
            if (strval($format) == "0")
            {
                echo hash($algo, $plaintext) . "\n";
            }
            elseif (strval($format) == "1")
            {
                echo hash($algo, $plaintext) . ":" . $algo . "\n";
            }
            elseif (strval($format) == "2")
            {
                echo hash($algo, $plaintext) . ":" . $plaintext . "\n";
            }
            elseif (strval($format) == "3")
            {
                echo $algo . ":" . hash($algo, $plaintext) . ":" . $plaintext . "\n";
            }
        }
    }
    else
    {
        echo "Error Occured!";
        generateHelp();
    }
}
