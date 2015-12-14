<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Estudiantes_model extends CI_Model {
    var $table = 'estudiantes';
    var $column = array('lu_alu','apellido_alu','nom_alu');
        // ,'address','dob');
    var $order = array('lu_alu' => 'desc');
 
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
 
    private function _get_datatables_query()
    {
        $this->db->from($this->table);
        $i = 0;
        foreach ($this->column as $item)
        {
            if($_POST['search']['value'])
                ($i===0) ? $this->db->like($item, $_POST['search']['value']) : $this->db->or_like($item, $_POST['search']['value']);
            $column[$i] = $item;
            $i++;
        }
 
        if(isset($_POST['order']))
        {
            $this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
 
    function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
 
    public function get_by_lu_alu($lu_alu)
    {
        $this->db->from($this->table);
        $this->db->where('lu_alu',$lu_alu);
        $query = $this->db->get();
 
        return $query->row();
    }
 
    public function save($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
 
    public function update($where, $data)
    {
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }
 
    public function delete_by_lu_alu($lu_alu)
    {
        $this->db->where('lu_alu', $lu_alu);
        $this->db->delete($this->table);
    }
  //versiones viejas

    // function getData() 
    // {    
 //         $estudiantes = $this->db->get('estudiantes'); //obtenemos la tabla 'estudiantes'. db->get('nombre_tabla') equivale a SELECT * FROM nombre_tabla.
 
    //  return $estudiantes->result(); //devolvemos el resultado de lanzar la query.
    //  }



    public function guardar_alumno($lu_alu, $apellido_alu,$nom_alu)
    {
        //Verifico que no exista un alumno con el mismo legajo
        $query_string = "SELECT apellido_alu FROM estudiantes
                WHERE lu_alu = ? ";
        $query = $this->db->query($query_string,array($lu_alu));
        if($this->db->affected_rows() > 0) 
        {
            $exam = $query->row_array();    
            throw new Exception(ERROR_REPETIDO);
        }
        //Inserto info en la tabla alumno
        $query_string = "INSERT INTO estudiantes (lu_alu,apellido_alu,nom_alu) 
             VALUES (?,?,?)";
        $this->db->query($query_string,array($lu_alu, $apellido_alu,$nom_alu));
        
        
        if($this->db->affected_rows() == 0)
        {
            throw new Exception(ERROR_NO_INSERT_EXAM);
        }
        //$id_exam = $this->db->insert_id();
    }
    /**
     *  Elimina un alumno.
     *
     * @access  public
     * @param   int $lu_alu
     * 
     *
     */

    public function eliminar_alumno($lu_alu)
    {
        //Verifico que exista un guia con el mismo id_guia
        $query_string = "SELECT lu_alu FROM estudiantes
                WHERE lu_alu = ? ";
        $query = $this->db->query($query_string,array($lu_alu));
        if($this->db->affected_rows() == 0) 
        {
            $alu = $query->row_array(); 
            throw new Exception(ERROR_REPETIDO); //cambiar error
        }
        else{
            $query_string = "SELECT lu_alu FROM estudiantes_catedras
                WHERE lu_alu = ? ";
            $query = $this->db->query($query_string,array($lu_alu));
            if($this->db->affected_rows() > 0) 
            {   

                $query_string = "DELETE FROM estudiantes_catedras WHERE lu_alu = ?";
                $this->db->query($query_string,array($lu_alu));
                }

        }
        $query_string = "DELETE FROM estudiantes WHERE lu_alu = ?";
        $this->db->query($query_string,array($lu_alu));
    }

    /**
     *  Obtiene lista de estudiantes.
     *
     * @access  public
     * @param   
     * @return  array de array - datos de los estudiantes
     *
     */

    public function get_estudiantes()
    {

        $query_string = "SELECT lu_alu,apellido_alu,nom_alu
                            FROM estudiantes ";
        $query = $this->db->query($query_string);
        return $query->result_array();
    }


    /**
     *  Retorna todos los estudiantes asociados a la catedra indicada 
     *
     * @access  public
     * @param   $cod_catedra int codigo de la catedra
     * @return  array - datos de los estudiantes asociados a la catedra
     *
     */
    public function get_estudiantes_catedra($cod_catedra)
    {
        $query_string = "SELECT DISTINCT lu_alu,apellido_alu,nom_alu,year_alu_cat,periodo_alu_cat
                FROM estudiantes NATURAL JOIN estudiantes_catedras 
                WHERE cod_cat = ? ORDER BY lu_alu ASC";
        $query = $this->db->query($query_string,array($cod_catedra));
    
        return $query->result_array();
    }

    public function get_estudiantes_not_catedra($cod_catedra){
         
        $query_string = " SELECT lu_alu,apellido_alu,nom_alu FROM estudiantes WHERE NOT EXISTS( SELECT lu_alu FROM estudiantes_catedras WHERE ( estudiantes.lu_alu = estudiantes_catedras.lu_alu AND estudiantes_catedras.cod_cat = ?))";
        $query = $this->db->query($query_string,array($cod_catedra));
    
        return $query->result_array();
    }

    /**
     *  Retorna el alumno de lu indicado, verificando que este asociado a la catedra 
     *
     * @access  public
     * @param   $lu_alu int lu alumno
     * @param   $cod_catedra int codigo de la catedra
     * @return  array - datos de los estudiantes asociados a la catedra
     *
     */

    public function get_alumno_catedra($lu_alu,$cod_catedra)
    {
        $query_string = "SELECT DISTINCT lu_alu,apellido_alu,nom_alu
                FROM estudiantes NATURAL JOIN estudiantes_catedras 
                WHERE lu_alu = ? AND cod_cat = ?";
        $query = $this->db->query($query_string,array($lu_alu,$cod_catedra));
    
        return $query->row_array();
    }

    /**
     *  Verificando que el alumno este asociado a la catedra 
     *
     * @access  public
     * @param   $lu_alu int lu alumno
     * @param   $cod_catedra int codigo de la catedra
     * @return  TRUE: alumno asociado a catedra | FALSE: caso contrario.
     *
     */

    public function check_alumno_catedra($lu_alu,$cod_catedra)
    {
        $query_string = "SELECT * FROM estudiantes_catedras
                WHERE lu_alu = ? AND cod_cat = ?";
        $query = $this->db->query($query_string,array($lu_alu,$cod_catedra));
    
        return $query->num_rows()>0;
    }

    /**
     *  Vincula un alumno con una cátedra
     *
     * @access  public
     * @param   $lu_alu int id del alumno
     * @param   $cod_cat int id de la guia
     *
     */
    public function vincular_alumno_catedra($lu_alu, $anio_alu_cat, $per_alu_cat, $cod_cat)//($id_item, $id_guia)
    {

        //Verifico que no exista el alumno en la catedra
        $query_string = "SELECT lu_alu FROM estudiantes_catedras
                WHERE lu_alu = ? AND cod_cat = ? AND year_alu_cat = ? AND periodo_alu_cat = ?";
        $query = $this->db->query($query_string,array($lu_alu,$cod_cat,$anio_alu_cat, $per_alu_cat));
        if($this->db->affected_rows() > 0) 
        {
            $exam = $query->row_array();    
            throw new Exception(ERROR_REPETIDO);
        }   
        //Inserto info en la tabla items_guias
        $query_string = "INSERT INTO estudiantes_catedras(lu_alu,cod_cat,year_alu_cat,periodo_alu_cat) VALUES (?,?,?,?);";

        $this->db->query($query_string,array($lu_alu,$cod_cat,$anio_alu_cat, $per_alu_cat));
    

    }

        /**  year_alu_cat VARCHAR(4) NOT NULL,
    
     *  Elimina un alumno de cátedra.
     *
     * @access  public
     * @param   int $lu_alu
     * 
     *
     */

    public function eliminar_alumno_catedra($lu_alu, $cod_cat)
    {
        //Verifico que exista un alumno con el mismo lu_alu
        $query_string = "SELECT lu_alu FROM estudiantes_catedras
                WHERE lu_alu = ? ";
        $query = $this->db->query($query_string,array($lu_alu));
        if($this->db->affected_rows() == 0) 
        {

            $exam = $query->row_array();    
            throw new Exception(ERROR_REPETIDO); //cambiar error
        }
        else{
            $query_string = "SELECT cod_cat FROM estudiantes_catedras
                WHERE cod_cat = ? ";
            $query = $this->db->query($query_string,array($cod_cat));
            if($this->db->affected_rows() > 0) 
            {   
                $query_string = "DELETE FROM estudiantes_catedras WHERE lu_alu = ?";
                $this->db->query($query_string,array($lu_alu));
            }

        }
    }
        /**
     *  Retorna el alumno con el id seleccionado
     *
     * @access  public
     * @param   $cod_cat int codigo de la catedra
     * @param   $id_guia int id de la guia
     * @return  array - dato de las guia
     *
     */

    public function get_alumno($lu)
    {
        $query_string = "SELECT lu_alu, apellido_alu,nom_alu  FROM estudiantes 
                WHERE lu_alu = ?";
        $query = $this->db->query($query_string,$lu);
    
        return $query->row_array();
    }


/**
     *  Actualiza un alumno
     *
     * @access  public
     * @param   int $leg
     * 
     *
     */

    public function actualizar_alumno($leg,$apellido,$nom)
    {
        // UPDATE table_name
        //  SET column_name = value
        //  WHERE condition

        //Verifico que exista un alumno con el mismo legajo
        $query_string = " UPDATE estudiantes
            SET apellido_alu = ?, nom_alu = ?
            WHERE lu_alu = ?";
        $query = $this->db->query($query_string,array($apellido,$nom,$leg));
        // if($this->db->affected_rows() == 0) 
        // {
        //  $exam = $query->row_array();    
        //  throw new Exception(ERROR_REPETIDO); //cambiar error
        // }
        
    }   


        /**
     *  Retorna todos los estudiantes asociados a la catedra indicada 
     *
     * @access  public
     * @param   $cod_catedra int codigo de la catedra
     * @return  array - datos de los estudiantes asociados a la catedra
     *
     */
    public function get_examenes_alumno($lu)
    {
        $query_string = "SELECT DISTINCT lu_alu,apellido_alu,nom_alu,examenes.id_guia,fecha,calificacion,porcentaje_exam,id_exam, tit_guia FROM estudiantes NATURAL JOIN examenes NATURAL LEFT JOIN guias WHERE lu_alu = ?  ORDER BY fecha ASC ";
        $query = $this->db->query($query_string,array($lu));
    
        return $query->result_array();
    }
}