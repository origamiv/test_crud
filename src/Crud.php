<?php

namespace YourResult;

use mysql_xdevapi\Exception;
use PDO;

class Crud
{
    public $db;
    public $options;
    public $url;
    public $url_parts;
    public $request;
    public $is_post = false;

    function __construct(\PDO $db = null)
    {
        //echo "create";
        if ($db)
        {
            $this->db = $db;
        }

        $this->request = $_REQUEST;
        if (!empty($_POST))
        {
            $this->is_post = true;
        }
        $this->url = $_SERVER['REQUEST_URI'];
        $parts = parse_url($this->url);

        $parts['params'] = explode('/', $parts['path']);
        unset($parts['params'][0]);
        //
        $this->url_parts = $parts;
        //print_r($this->url_parts);
        $this->init();
        $this->route();
        // запуск метода run после роутинга
        if (filter_var($_ENV['RUN'], FILTER_VALIDATE_BOOLEAN) == true)
        {
            $this->run();
        }
    }

    function init()
    {

    }

    function route()
    {
        $name = $this->url_parts['params'][1];
        switch ($name)
        {
            case '':
                include_once 'main.html';
                break;
            default:
                $this->$name();
                break;
        }
    }

    function run()
    {
        echo "run";
    }

    function __call($name, $arguments)
    {
        echo $name;
    }

    function replace()
    {
        $result = $this->checkRequest(true);
        if ($result)
        {
            $errors = [];
            try
            {
                if (!isset($this->request['id']))
                {
                    $errors[] = 'Нужно id точки';
                }
                if (count($errors) == 0)
                {
                    $this->delete(true);
                    $this->create();
                }
                else
                {
                    $result = [];
                    $result['error'] = $errors;
                    $result['status'] = false;
                    echo json_encode($result, JSON_UNESCAPED_UNICODE);
                }
            } catch (Exception $exception)
            {
                $result['error'] = $exception->getMessage();
                $result['status'] = false;
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            }

        }

    }

    function checkRequest($is_post = true)
    {
        if ($is_post)
        {
            if ($this->is_post == true)
            {
                return true;
            }
            else
            {
                $result['error'] = 'Требуется передача методом POST';
                $result['status'] = false;
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            }
        }
        else
        {
            if ($this->is_post == false)
            {
                return true;
            }
            else
            {
                $result['error'] = 'Требуется передача методом GET';
                $result['status'] = false;
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            }
        }
    }

    function delete($retunResult = false)
    {
        $result = $this->checkRequest(true);
        if ($result)
        {
            $errors = [];
            try
            {
                if (!isset($this->request['id']))
                {
                    $errors[] = 'Нужно id точки';
                }

                if (count($errors) == 0)
                {

                    $id = $this->request['id'];
                    unset($this->request['id']);
                    $sql = "DELETE FROM `points` WHERE id=$id";
                    $this->db->query($sql);

                    $result = [];
                    $result['error'] = '';
                    $result['status'] = true;
                    $json = json_encode($result, JSON_UNESCAPED_UNICODE);
                }
                else
                {
                    $result = [];
                    $result['error'] = $errors;
                    $result['status'] = false;
                    $json = json_encode($result, JSON_UNESCAPED_UNICODE);
                }
                if ($retunResult == true)
                {
                    return $json;
                }
                else
                {
                    echo $json;
                }
            } catch (Exception $exception)
            {
                $result['error'] = $exception->getMessage();
                $result['status'] = false;
                $json = json_encode($result, JSON_UNESCAPED_UNICODE);
                if ($retunResult == true)
                {
                    return $json;
                }
                else
                {
                    echo $json;
                }
            }

        }

    }

    function create()
    {
        $result = $this->checkRequest(true);
        if ($result)
        {
            $errors = [];
            try
            {
                if (!isset($this->request['name']))
                {
                    $errors[] = 'Нужно передать название';
                }
                if (!isset($this->request['address']))
                {
                    $errors[] = 'Нужно передать адрес';
                }
                if (count($errors) == 0)
                {
                    $name = $this->request['name'];
                    $address = $this->request['address'];
                    $sql
                        = "INSERT INTO `points`(`name`, `address`) VALUES ('$name', '$address')";
                    $this->db->query($sql);
                    $result = [];
                    $result['error'] = '';
                    $result['status'] = true;
                    echo json_encode($result, JSON_UNESCAPED_UNICODE);
                }
                else
                {
                    $result = [];
                    $result['error'] = $errors;
                    $result['status'] = false;
                    echo json_encode($result, JSON_UNESCAPED_UNICODE);
                }
            } catch (Exception $exception)
            {
                $result['error'] = $exception->getMessage();
                $result['status'] = false;
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            }

        }

    }

    function list()
    {
        $result = $this->checkRequest(false);
        if ($result)
        {
            $result = [];
            $sql = "SELECT * FROM `points`";
            $result['data'] = $this->db->query($sql, PDO::FETCH_ASSOC)
                ->fetchAll();
            $result['status'] = true;
            echo json_encode($result);
        }
    }

    function update($returnResult = false)
    {
        $result = $this->checkRequest(true);
        if ($result)
        {
            $errors = [];
            try
            {
                if (!isset($this->request['id']))
                {
                    $errors[] = 'Нужно id точки';
                }
                if (!isset($this->request['name'])
                    && (!isset($this->request['address']))
                )
                {
                    $errors[] = 'Нужно передать название или адрес';
                }
                if (count($errors) == 0)
                {

                    $id = $this->request['id'];
                    unset($this->request['id']);
                    foreach ($this->request as $key => $value)
                    {
                        $sql
                            = "UPDATE `points` SET `$key`='$value' WHERE id=$id";
                        $this->db->query($sql);
                    }

                    $result = [];
                    $result['error'] = '';
                    $result['status'] = true;
                    $json = json_encode($result, JSON_UNESCAPED_UNICODE);
                }
                else
                {
                    $result = [];
                    $result['error'] = $errors;
                    $result['status'] = false;
                    $json = json_encode($result, JSON_UNESCAPED_UNICODE);
                }
                if ($returnResult == true)
                {
                    return $json;
                }
                else
                {
                    echo $json;
                }
            } catch (Exception $exception)
            {
                $result['error'] = $exception->getMessage();
                $result['status'] = false;
                $json = json_encode($result, JSON_UNESCAPED_UNICODE);
                if ($returnResult == true)
                {
                    return $json;
                }
                else
                {
                    echo $json;
                }
            }

        }

    }


}