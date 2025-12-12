<?php
include("motor.php");

class Libro_imp {
    public $id_libro;
    public $titulo;
    public $autor;
    public $edicion;
    public $anio;
    public $area;
    public $ejemplares;
    
    function guardar() {
        $sql = "INSERT INTO libros_imp (titulo, autor, edicion, anio, area, ejemplares) 
                VALUES ('$this->titulo', '$this->autor', '$this->edicion', '$this->anio', '$this->area', $this->ejemplares)";
        $objConn = new Conexion();
        $objConn->enlace->query($sql);
    }
    
    function actualizar($nro = 0) {
        $sql = "UPDATE libros_imp SET 
                titulo='$this->titulo', autor='$this->autor', edicion='$this->edicion', 
                anio='$this->anio', area='$this->area', ejemplares=$this->ejemplares 
                WHERE id_libro = $nro";
        $objConn = new Conexion();
        $objConn->enlace->query($sql);
    }
    
    function borrar($nro = 0) {
        $sql = "DELETE FROM libros_imp WHERE id_libro = $nro";
        $objConn = new Conexion();
        $objConn->enlace->query($sql);
    }
    
    function traer_datos($nro = 0) {
        if ($nro != 0) {
            $sql = "SELECT * FROM libros_imp WHERE id_libro = $nro";
            $objConn = new Conexion();
            $result = $objConn->enlace->query($sql);
            return mysqli_fetch_array($result);
        }
        return null;
    }
    
    static function mostrar_todos() {
        $sql = "SELECT * FROM libros_imp ORDER BY titulo";
        $objConn = new Conexion();
        $rs = $objConn->enlace->query($sql);
        $lib = array();
        while($fila = mysqli_fetch_assoc($rs)) {
            $lib[] = $fila;
        }
        return $lib;
    }
    
    static function buscar($str) {
        $sql = "SELECT * FROM libros_imp WHERE 
                titulo LIKE '%$str%' OR autor LIKE '%$str%' OR area LIKE '%$str%' 
                OR id_libro='$str' ORDER BY titulo";
        $objConn = new Conexion();
        $rs = $objConn->enlace->query($sql);
        $lib = array();
        while($fila = mysqli_fetch_assoc($rs)) {
            $lib[] = $fila;
        }
        return $lib;
    }
    
    static function ejemplares_disponibles($id_libro) {
        $sql = "SELECT l.ejemplares, COUNT(p.id_prestamo) as prestados 
                FROM libros_imp l 
                LEFT JOIN prestamos_imp p ON l.id_libro = p.id_libro AND p.devuelto = 0 
                WHERE l.id_libro = $id_libro 
                GROUP BY l.id_libro";
        $objConn = new Conexion();
        $result = $objConn->enlace->query($sql);
        if ($row = mysqli_fetch_assoc($result)) {
            return max(0, $row['ejemplares'] - $row['prestados']);
        }
        return 0;
    }
}

class Prestamo_imp {
    public $id_prestamo;
    public $id_libro;
    public $id_socio;
    public $fecha_prestamo;
    public $fecha_devolucion;
    public $devuelto;
    
    function guardar() {
        $sql = "INSERT INTO prestamos_imp (id_libro, id_socio, fecha_prestamo, devuelto) 
                VALUES ($this->id_libro, $this->id_socio, '$this->fecha_prestamo', 0)";
        $objConn = new Conexion();
        $objConn->enlace->query($sql);
        return mysqli_insert_id($objConn->enlace);
    }
    
    function devolver($nro = 0) {
        $fecha = date('Y-m-d');
        $sql = "UPDATE prestamos_imp SET devuelto=1, fecha_devolucion='$fecha' WHERE id_prestamo = $nro";
        $objConn = new Conexion();
        $objConn->enlace->query($sql);
    }
    
    static function prestamos_activos() {
        $sql = "SELECT p.*, l.titulo, per.user, per.nombre, per.apellido 
                FROM prestamos_imp p 
                JOIN libros_imp l ON p.id_libro = l.id_libro 
                JOIN personas per ON p.id_socio = per.id 
                WHERE p.devuelto = 0 
                ORDER BY p.fecha_prestamo DESC";
        $objConn = new Conexion();
        $rs = $objConn->enlace->query($sql);
        $prestamos = array();
        while($fila = mysqli_fetch_assoc($rs)) {
            $prestamos[] = $fila;
        }
        return $prestamos;
    }
    
    static function prestamos_por_socio($id_socio) {
        $sql = "SELECT p.*, l.titulo, l.autor 
                FROM prestamos_imp p 
                JOIN libros_imp l ON p.id_libro = l.id_libro 
                WHERE p.id_socio = $id_socio AND p.devuelto = 0 
                ORDER BY p.fecha_prestamo DESC";
        $objConn = new Conexion();
        $rs = $objConn->enlace->query($sql);
        $prestamos = array();
        while($fila = mysqli_fetch_assoc($rs)) {
            $prestamos[] = $fila;
        }
        return $prestamos;
    }
}

