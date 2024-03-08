<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; chaset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With");

    $method = $_SERVER["REQUEST_METHOD"];

    include("../../connection/connection.php");
    include("../../valida_token.php");

    if($method == "GET"){
        // echo "GET";

        if(!isset($_GET["id"])){

            //Listar todos os registros
            try {
                $sql = "SELECT pk_cor, cor
                        FROM cor";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                
                $dados = $stmt->fetchall(PDO::FETCH_OBJ);
                // echo "<pre>";
                // var_dump($dados);
                // echo "</pre>"; 


                $result["cor"]=$dados;
                $result["status"] = "success";

                http_response_code(200);
            } 

            catch (PDOException $ex) {
                // echo "error: $ex->getMessage()";
                $result = ["status"=> "fail", "Error"=> $ex->getMessage()];
                http_response_code(200);
            }

            finally{
                $conn = null;
                echo json_encode($result);
            }
        }
        else{
            //Listar um registro
            try{
                if(empty($_GET["id"]) || !is_numeric($_GET["id"])){
                //Está vazio ou não é númerico: ERRO
                throw new ErrorException("Valor inválido", 1);
                }

                $id = $_GET["id"];

                $sql = "SELECT pk_cor, cor 
                        FROM produtos 
                        WHERE pk_cor=:id";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":id", $id);
                $stmt->execute();

                $dado = $stmt->fetch(PDO::FETCH_OBJ);

                $result['cor'] = $dado;
                $result["status"] = "success";
            }
            
            catch (PDOException $ex) {
                $result = ["status"=> "fail", "Error"=> $ex->getMessage()];
                http_response_code(200);
            }

            catch(Exception $ex){
                $result = ["status"=> "fail", "Error"=> $ex->getMessage()];
                http_response_code(200);
            }

            finally{
                $conn = null;
                echo json_encode($result);
            }
        }

    }

    if($method == "POST"){
        //Recupera dados do corpo (body) de uma requisição POST
        $dados = file_get_contents("php://input");

        //Decodifica JSON, sem opção TRUE
        $dados = json_decode($dados);  //Isso retorna um OBJETO

        //Função TRIM retira espaços que estão sobrando
            $cor = trim($dados->cor);   //Acessa o valor de um OBJETO 

        try {
            if(empty($cor)){
                //Está vazio ou não é númerico: ERRO
                throw new ErrorException("Valor inválido", 1);
            }

            $sql = "INSERT INTO cor(cor) 
                    VALUES (:cor)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":cor", $cor);
            $stmt->execute();

            $result = array("status"=>"success");
        } 
        
        catch (PDOException $ex) {
            $result = ["status"=> "fail", "Error"=> $ex->getMessage()];
                http_response_code(200);
            }
            catch(Exception $ex){
                $result = ["status"=> "fail", "Error"=> $ex->getMessage()];
                http_response_code(200);
            }
            finally{
                $conn = null;
                echo json_encode($result);
            }
    }

    if($method == "PUT"){
        //Recupera dados do corpo (body) de uma requisição PUT
        $dados = file_get_contents("php://input");

        //Decodifica JSON, sem opção TRUE
        $dados = json_decode($dados);
        //Isso retorna um OBJETO
        try {

            if(empty($dados->cor)){
                //Está vazio: ERRO
                throw new ErrorException("Cor é um campo obrigatório", 1);
            }

            if(empty($dados->id)){
                //Está vazio: ERRO
                throw new ErrorException("ID inválido", 1);
            }

            //Função TRIM retira espaços que estão sobrando
            $cor = trim($dados->cor);  //Acessa o valor de um OBJETO 
            $id = trim($dados->id);

            $sql = "UPDATE cor 
                    SET cor=:cor 
                    WHERE pk_cor=:id";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":cor", $cor);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $result = array("status"=>"success");
        } 
        
        catch (PDOException $ex) {
            $result = ["status"=> "fail", "Error"=> $ex->getMessage()];
                http_response_code(200);
            }
            catch(Exception $ex){
                $result = ["status"=> "fail", "Error"=> $ex->getMessage()];
                http_response_code(200);
            }
            finally{
                $conn = null;
                echo json_encode($result);
            }
    }

    if($method == "DELETE"){
            try{
                if(empty($_GET["id"]) || !is_numeric($_GET["id"])){
                //Está vazio ou não é númerico: ERRO
                throw new ErrorException("Valor inválido", 1);
                }

                $id = $_GET["id"];

                $sql = "DELETE FROM cor 
                        WHERE pk_cor=:id";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":id", $id);
                $stmt->execute();

                $result["status"] = "success";
            }
            
            catch (PDOException $ex) {
                $result = ["status"=> "fail", "Error"=> $ex->getMessage()];
                http_response_code(200);
            }

            catch(Exception $ex){
                $result = ["status"=> "fail", "Error"=> $ex->getMessage()];
                http_response_code(200);
            }

            finally{
                $conn = null;
                echo json_encode($result);
            }
    }
?>