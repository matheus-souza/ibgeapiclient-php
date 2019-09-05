<?php
/**
 * 2019
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Nibblelab Tecnologia LTDA
 * @copyright 2019 Nibblelab Tecnologia LTDA
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 *
 */

namespace IBGEApiClient\MicroRegiao;

use \IBGEApiClient\Error\Errors;
use \IBGEApiClient\Request\HTTPMethod;
use \IBGEApiClient\Request\Request;
use \IBGEApiClient\Response\Response;
use \IBGEApiClient\MicroRegiao\Response\MicroRegiaoResponseData;
use \IBGEApiClient\MesoRegiao\MesoRegiaoRequest;

/**
 * Requisição de lista das microregiões
 */
class MicroRegiaoRequest extends Request
{
    /**
     *
     * @var \IBGEApiClient\MesoRegiao\MesoRegiaoRequest 
     */
    private $meso_req;
    
    /**
     * Processa a resposta da requisição
     * 
     * @param string $json_str string com o json de resposta
     * @return \IBGEApiClient\Response\Response
     * @throws \Exception
     */
    private function decodeResponse($json_str): \IBGEApiClient\Response\Response
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR,$json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new Response();
        
        $result = array();
        $this->prepareAux();
        if(is_array($result_json)) {
            foreach($result_json as $data) {
                $result[] = $this->mountMicroRegiao($data);
            }
        }
        else {
            $result[] = $this->mountMicroRegiao($result_json);
        }
        
        $response->setData($result);
        
        return $response;
    }
    
    /**
     * Prepare os objetos auxiliares
     * 
     * @return void
     */
    public function prepareAux(): void
    {
        $this->meso_req = new MesoRegiaoRequest();
        $this->meso_req->prepareAux();
    }
    
    /**
     * Gere os dados da micorregião a partir do objeto json
     * 
     * @param object $micro_regiao_obj
     * @return \IBGEApiClient\MicroRegiao\Response\MicroRegiaoResponseData
     */
    public function mountMicroRegiao($micro_regiao_obj): \IBGEApiClient\MicroRegiao\Response\MicroRegiaoResponseData
    {
        $micro_regiao_data = new MicroRegiaoResponseData();
        $micro_regiao_data->setId($micro_regiao_obj->id);
        $micro_regiao_data->setNome($micro_regiao_obj->nome);
        $micro_regiao_data->setMesorregiao($this->meso_req->mountMesoRegiao($micro_regiao_obj->mesorregiao));
        return $micro_regiao_data;
    }
    
    /**
     * requisição
     * 
     * @param array $config dados de configuração 
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function request(array $config): ?\IBGEApiClient\Response\Response
    {
        try {
            $result = $this->doRequest($config['url'] . 'localidades/microrregioes', HTTPMethod::GET);
            return $this->decodeResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    /**
     * requisição por UF
     * 
     * @param array $config dados de configuração 
     * @param string $uf id da uf
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function requestByUF(array $config, $uf): ?\IBGEApiClient\Response\Response
    {
        try {
            $result = $this->doRequest($config['url'] . 'localidades/estados/' . $uf . '/microrregioes', HTTPMethod::GET);
            return $this->decodeResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    /**
     * requisição por UFs
     * 
     * @param array $config dados de configuração 
     * @param array $ufs array com os ids das ufs
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function requestByUFs(array $config, array $ufs): ?\IBGEApiClient\Response\Response
    {
        $uf = '';
        foreach($ufs as $_uf) {
            if(!empty($uf)) {
                $uf .= '|';
            }
            $uf .= $_uf;
        }
        return $this->requestByUF($config, $uf);
    }
    
    /**
     * requisição por mesorregião
     * 
     * @param array $config dados de configuração 
     * @param string $mesorregiao id da mesorregião
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function requestByMesoRegiao(array $config, $mesorregiao): ?\IBGEApiClient\Response\Response
    {
        try {
            $result = $this->doRequest($config['url'] . 'localidades/mesorregioes/' . $mesorregiao . '/microrregioes', HTTPMethod::GET);
            return $this->decodeResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    /**
     * requisição por mesorregiões
     * 
     * @param array $config dados de configuração 
     * @param array $mesorregioes array com os ids das mesorregiões
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function requestByMesoRegioes(array $config, $mesorregioes): ?\IBGEApiClient\Response\Response
    { 
        $mesorregiao = '';
        foreach($mesorregioes as $_mesorregiao) {
            if(!empty($mesorregiao)) {
                $mesorregiao .= '|';
            }
            $mesorregiao .= $_mesorregiao;
        }
        return $this->requestByMesoRegiao($config, $mesorregiao);
    }
    
    /**
     * requisição por região
     * 
     * @param array $config dados de configuração 
     * @param string $regiao id da microrregião
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function requestByRegiao(array $config, $regiao): ?\IBGEApiClient\Response\Response
    { 
        try {
            $result = $this->doRequest($config['url'] . 'localidades/regioes/' . $regiao . '/microrregioes', HTTPMethod::GET);
            return $this->decodeResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    /**
     * requisição por regiões
     * 
     * @param array $config dados de configuração 
     * @param array $regioes array com os ids das regiões
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function requestByRegioes(array $config, $regioes): ?\IBGEApiClient\Response\Response
    { 
        $regiao = '';
        foreach($regioes as $_regiao) {
            if(!empty($regiao)) {
                $regiao .= '|';
            }
            $regiao .= $_regiao;
        }
        return $this->requestByRegiao($config, $regiao);
    }
    
    /**
     * requisição por id
     * 
     * @param array $config dados de configuração 
     * @param string $id 
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function requestById(array $config, $id): ?\IBGEApiClient\Response\Response
    {
        try {
            $result = $this->doRequest($config['url'] . 'localidades/microrregioes/' . $id, HTTPMethod::GET);
            return $this->decodeResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    /**
     * requisição por ids
     * 
     * @param array $config dados de configuração 
     * @param array $ids array com os ids 
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function requestByIds(array $config, $ids): ?\IBGEApiClient\Response\Response
    { 
        $id = '';
        foreach($ids as $_id) {
            if(!empty($id)) {
                $id .= '|';
            }
            $id .= $_id;
        }
        return $this->requestById($config, $id);
    }
}
