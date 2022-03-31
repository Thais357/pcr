<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Viajeros;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Knp\Snappy\Pdf;
use ArrayObject;
use App\Form\ViajerosType;
use PhpOffice\PhpSpreadsheet\Reader\Ods as ReaderXls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;

class ViajerosController extends AbstractController
{
  private $twig;
  private $pdf;
  public function __construct(MailerInterface $mailer, Environment $twig, Pdf $pdf)
  {
      $this->twig = $twig;
      $this->pdf = $pdf;
  }
    /**
     * @Route("/viajeros", name="viajeros")
     */
    public function index(): Response
    {
       $entityManager=$this->getDoctrine()->getManager();
        $viajeros=$this->getDoctrine()->getManager()->getRepository(Viajeros::class)->findAll();
        $nombreMenu="Resultados";
        $totalResultados=count($viajeros);
        return $this->render('viajeros/index.html.twig', [
            'datos' => $viajeros,'nombreMenu'=>$nombreMenu,'total'=>$totalResultados
        ]);
    }
    /**
     * @Route("viajeros/notificados",name="viajero_notificados")
     */
    public function viajerosNotificados(){
      
      $entityManager=$this->getDoctrine()->getManager();
      $viajeros=$entityManager->getRepository(Viajeros::class)->findBy(['notificado'=>'si']);
      $nombreMenu="Resultados notificados";
      $totalResultados=count($viajeros);
      return $this->render('viajeros/index.html.twig', [
        'datos' => $viajeros,'nombreMenu'=>$nombreMenu,'total'=>$totalResultados
    ]);
    }
/**
     * @Route("viajeros/sin_notificar",name="viajeros_sin_notificar")
     */
    public function viajerosSinNotificar(){
      $entityManager=$this->getDoctrine()->getManager();
      $viajeros=$entityManager->getRepository(Viajeros::class)->findBy(['notificado'=>'no']);
      $nombreMenu="Resultados sin notificar";
      $totalResultados=count($viajeros);
      return $this->render('viajeros/index.html.twig', [
        'datos' => $viajeros,'nombreMenu'=>$nombreMenu,'total'=>$totalResultados
    ]);
    }
    /**
     * @Route("viajeros/cargar_resultados",name="cargar_resultados")
     */
    public function pantallaCargar(){
      return $this->render('viajeros/carga_datos.html.twig');
    }
    /**
     * @Route("/pcr/enviarCorreoPCR", name="enviarCorreoPCR")
     */
    public function enviarCorreo(MailerInterface $mailer, \Knp\Snappy\Pdf $snappy){ 

      $sinNotificar=$this->getDoctrine()->getManager()->getRepository(Viajeros::class)->findBy(['notificado'=>'no']);
      $entityManager=$this->getDoctrine()->getManager();
     foreach($sinNotificar as $v){
   //   dump($v);
      $email = $v->getCorreo();
      //Generar pdf
      $html = $this->twig->render('viajeros/pcrPdf.html.twig', ['datosViajero' => $v]);
      $pdf = $this->pdf->getOutputFromHtml($html);
      $message = (new TemplatedEmail())
       ->from('pcrresult@cicc.cu')
       ->to($email)
       ->addBcc('thaisalonso@infomed.sld.cu')
//  ->bcc('lazaro.perez@cicc.cu','aixa.mira@cicc.cu','ilianed.puig@cicc.cu','ilianed.puig@infomed.sld.cu','elsa.rodriguez@cicc.cu','thais.alonso@cicc.cu','aime.mompie@cicc.cu'))
       ->subject('Resultados de PCR'.' '.$v->getNombre())
       ->htmlTemplate('viajeros/pcrEmail.html.twig')
       ->context([
         'viajero' => $v,
        ])
       ->attach($pdf, sprintf('resultado.pdf', date('Y-m-d')));

       try {
       $mailer->send($message);
       $v->setNotificado("si");
       $this->addFlash('success', 'Paciente notificado via correo correctamente');
      } catch (TransportExceptionInterface $e) {
      $this->addFlash('danger',$e);
      }
 
     $entityManager->persist($v);
  
     }
     //dump("fin iteracion");die();

     $entityManager->flush();
     
      
      return $this->redirectToRoute('viajeros');

     }
    /**
     * @Route("/pcr/leerExcel", name="importarPCR")
     */
    public function leerExcel(Request $request){
      $archivo= $request->files->get('fileToUpload');
      $filename=$archivo->getClientOriginalName();
      $entityManager=$this->getDoctrine()->getManager();
    
    
      $extension = pathinfo($filename, PATHINFO_EXTENSION);
      switch ($extension) {

        case 'xlsx':
            $reader = new ReaderXlsx();
            break;
            case 'xls':
            $reader = new ReaderXls();
            break;
        default:
            throw new \Exception('Extension no permitida');
    }


$name=explode(".",$filename);

// $reader = $this->get('phpspreadsheet')->createReader('Xlsx');
 //$reader = $this->get('phpspreadsheet')->createReader('Xlsx');
 $fileName=$name[0];
 $extension=$name[1];
 $file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($archivo);
 $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($file_type);
 //$reader->setInputEncoding('utf-8');
 
 $documento=$reader->load($archivo);
 $totalDeHojas = $documento->getSheetCount();
 $arrayResultados = new ArrayObject();
 # Iterar hoja por hoja
 for ($indiceHoja = 0; $indiceHoja < $totalDeHojas; $indiceHoja++) {
   # Obtener hoja en el índice que vaya del ciclo
   $hojaActual = $documento->getSheet($indiceHoja);
 //  echo "<h3>Vamos en la hoja con índice $indiceHoja</h3>";
   $contador=0;

   # Iterar filas
   foreach ($hojaActual->getRowIterator() as $fila) {
     $nuevoResultado=new ArrayObject();
     foreach ($fila->getCellIterator() as $celda) {
       // Aquí podemos obtener varias cosas interesantes
       #https://phpoffice.github.io/PhpSpreadsheet/master/PhpOffice/PhpSpreadsheet/Cell/Cell.html

       # El valor, así como está en el documento
       $valorRaw = $celda->getValue();

       # Formateado por ejemplo como dinero o con decimales
       $valorFormateado = $celda->getFormattedValue();

       # Si es una fórmula y necesitamos su valor, llamamos a:
       $valorCalculado = $celda->getCalculatedValue();

       # Fila, que comienza en 1, luego 2 y así...
       $fila = $celda->getRow();
       # Columna, que es la A, B, C y así...
       $columna = $celda->getColumn();


       $nuevoResultado->append($valorFormateado);
//          echo "En <strong>$columna$fila</strong> tenemos el valor <strong>$valorRaw</strong>. ";
//          echo "Formateado es: <strong>$valorFormateado</strong>. ";
//          echo "Calculado es: <strong>$valorCalculado</strong><br><br>";
     }
$arrayResultados->append($nuevoResultado);
   }
 }
// dump($arrayResultados);die();
 foreach ($arrayResultados as $r){
  //  dump($r);
    //die();
    $viajero=new Viajeros();
    $viajero->setIdIPK($r[0]);
//      dump($r[1]);
    $viajero->setNombre($r[1]);
    $viajero->setCi($r[2]);
    $correo=$this->obtenerCorreosAction($r[2])->getContent();
    $c=explode('"',$correo);

    if(sizeof($c)>1){
      $viajero->setCorreo($c[1]);
    }
    else{
      $viajero->setCorreo("no tiene");
    }
    $viajero->setFechaSalida($r[3]);
    $viajero->setResultado($r[4]);
    $viajero->setNotificado("no");
    $entityManager->persist($viajero);

  // dump($viajero);
  }
  $entityManager->flush();
  return $this->redirectToRoute('viajeros');
    }
    /**
   * @Route("/pcr/obtenerCorreosSIGH/{pasaporte}",name="obtenerCorreos")
   *  @Method("GET")
   * @return JsonResponse
   */
  public function obtenerCorreosAction($pasaporte)
  {
    /**
     * Configurar conexion al sql desde Windows (Liuben)
     */
    $serverName = "192.168.107.14";
    $database = "sighCICC";
    $uid = 'thais';
    $pwd = 'Thais2019**';
    try {
      $coneccionAssets = new \PDO(
        "mysql:host={$serverName};dbname=$database",
        $uid,
        $pwd,
        array(
          //PDO::ATTR_PERSISTENT => true,
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        )
      );


      /**
       * Consulta el correo segun el numero de pasaporte dado
       */
      //  $id_ccosto = '003';
      $sql="";

        if(ctype_digit($pasaporte)){
          $sql = "SELECT d.correo FROM datos_personales d WHERE  d.num_identidad=$pasaporte";
        }else{
          $sql = "SELECT d.correo FROM datos_personales d WHERE d.num_identidad = '$pasaporte'";
        }
        $query = $coneccionAssets->query($sql);
        if ($query) {
          $result = array();
          while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
            // dump($var);
            if($var['correo']==null){
              $result='No tiene';
            }else{
              $result = mb_convert_encoding($var['correo'], 'UTF-8', 'UTF-8');
            }

          }


        }
       // dump($result);die();
//        $responseArray = array();
//        foreach ($result as $res) {
//          $responseArray[] = array(
//            "correo" => $res['correo']);
//        }

          return new JsonResponse($result, 200, array('Content-Type'=>'application/json; charset=utf-8' ));


//       dump('entrandoooo yeaaaaaa');
//       die();
    } catch (\PDOException $e) {
      die("No se conecta con el servidor! - " . $e->getMessage());
    }
  }
    /**
     * @Route("viajeros/editar_datos/{id}",name="editar_datos")
     */
       public function pantallaEditarDatos($id){
        $entityManager=$this->getDoctrine()->getManager();
        $viajero=$entityManager->getRepository(Viajeros::class)->find($id);
        
        return $this->render('viajeros/editar_viajero.html.twig', [
          'datos' => $viajero,
      ]);
       }
       /**
     * @Route("viajeros/editar_datosViajero/{id}",name="editar_datosViajero")
     */
    public function EditarDatos(Request $request,$id){
      $entityManager=$this->getDoctrine()->getManager();
      $viajero=$entityManager->getRepository(Viajeros::class)->find($id);
      
   
        $form=$this->createForm(ViajerosType::class,$viajero);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
          $entityManager=$this->getDoctrine()->getManager();
    
          $entityManager->persist($viajero);
          $entityManager->flush();
          $this->addFlash('success', 'Usuario creado correctamente');
      
          return $this->redirectToRoute("viajeros");   
        }
         return $this->render('viajeros/editar_viajero.html.twig', [
            'controller_name' => 'UserController','formulario'=>$form->createView(),'datos'=>$viajero
        ]);
     
     }
    /**
     * @Route("/pcr/obtenerDatosViajero/{id}", name="obtenerDatosViajero")
     */
    public function obtenerDatos(Request $request,$id){
     /**
     * Configurar conexion al sql desde Windows (Liuben)
     */
    $serverName = "192.168.107.14";
    $database = "sighCICC";
    $uid = 'thais';
    $pwd = 'Thais2019**';
    try {
      $coneccionAssets = new \PDO(
        "mysql:host={$serverName};dbname=$database",
        $uid,
        $pwd,
        array(
          //PDO::ATTR_PERSISTENT => true,
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        )
      );
      $entityManager=$this->getDoctrine()->getManager();

      /**
       * Consulta el correo segun el numero de pasaporte dado
       */
      //  $id_ccosto = '003';
      $sqlPasaporte="";
      $viajero=$entityManager->getRepository(viajeros::class)->find($id);
      $nombre = $viajero->getNombre();
      $nombreS = explode(' ', $nombre);
      $longitudNombreA = sizeof($nombreS);
      $sqlPasaporte = '';
     // var_dump("La longitud del arreglo es:" . $longitudNombreA);
      //var_dump("No tiene pasaporte");
      switch ($longitudNombreA) {
        case 2:
          $sqlPasaporte = "SELECT d.nombre,d.apellido1,d.apellido2,d.num_identidad,d.correo FROM datos_personales d WHERE d.nombre LIKE '$nombreS[0]' AND  d.apellido1 = '$nombreS[1]'";
          break;
        case 3:
          $sqlPasaporte = "SELECT d.nombre,d.apellido1,d.apellido2,d.num_identidad,d.correo FROM datos_personales d WHERE d.nombre LIKE '%$nombreS[0]%' AND  d.apellido1 = '$nombreS[1]' AND  d.apellido2 ='$nombreS[2]' ";
         // var_dump($sqlPasaporte);
          break;
        case 4:
          $nombreCompleto = $nombreS[0] . ' ' . $nombreS[1];
        //  var_dump("El nombre completo es:" . $nombreCompleto);
          $sqlPasaporte = "SELECT d.nombre,d.apellido1,d.apellido2,d.num_identidad,d.correo FROM datos_personales d WHERE d.nombre LIKE '%$nombreCompleto%' AND  d.apellido1 = '$nombreS[2]' AND  d.apellido2 = '$nombreS[3]'";
       //   var_dump($sqlPasaporte);
          break;

      }
      $query = $coneccionAssets->query($sqlPasaporte);
     // dump($query->execute());
      if ($query) {
        $result = array();
        //dump($query->fetch(\PDO::FETCH_ASSOC));
        while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
          // dump($var);
          $viajero->setCi($var['num_identidad']);
        //  dump("hola");
         // dump($var);
         // if($var['num_identidad']!=""||$var['num_identidad']!=null){
         //   $result['ci']=$var['num_identidad'];
            $viajero->setCi($var['num_identidad']);
         // }

          if($var['correo']==null ){
            $result['correo']='No tiene';
            $this->addFlash('success', 'El paciente no tiene correo registrado');
          }else{
            $result['correo'] = mb_convert_encoding($var['correo'], 'UTF-8', 'UTF-8');

          }

          $viajero->setCorreo($result['correo']);
        }


      }
   //   dump($viajero);die();
      $entityManager->persist($viajero);
      $entityManager->flush();
      //return new JsonResponse($result, 200, array('Content-Type'=>'application/json; charset=utf-8' ));

      $this->addFlash('success', 'Viajeros actualizados correctamente');
      return $this->redirectToRoute('viajeros');
//       dump('entrandoooo yeaaaaaa');
//       die();
    } catch (\PDOException $e) {
      die("No se conecta con el servidor! - " . $e->getMessage());
    }
    }

    /**
     * @Route("/pcr/pdf/{id}", name="generarPdf")
     */
    public function generarPDF($id, \Knp\Snappy\Pdf $snappy){
        $viajero=$this->getDoctrine()->getRepository(viajeros::class)->find($id);
        // dump($viajero);die();
     
         //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
         $html = $this->renderView('viajeros/pcrPdf.html.twig', ['datosViajero' => $viajero ]);
     
         $filename = 'reporte';
     
         return new Response(
     
           $snappy->getOutputFromHtml($html), 200, array(
     
             'Content-Type' => 'application/pdf',
             'enable-javascript' => true,
     
             'page-size' => 'A4',
     
             'viewport-size' => '1280x1024',
             'margin-left' => '10mm',
     
             'margin-right' => '10mm',
     
             'margin-top' => '30mm',
     
             'margin-bottom' => '25mm',
             'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'
     
           )
     
         );
    }
    /**
   * @Route("/pcr/informes/pcrPdf/{id}",name="exportaPdf")
   */
  public function exportarPdf(Request $request,$id,MailerInterface $mailer, \Knp\Snappy\Pdf $snappy){
    $viajero=$this->getDoctrine()->getRepository(viajeros::class)->find($id);
    $entityManager=$this->getDoctrine()->getManager();
  
    $nombreFichero=$viajero->getId();
    $filename = 'reporte';
    $archivo="/pcrPdf/$nombreFichero/$nombreFichero.pdf";
    $existe=false;

    //Enviar correo
    $email = $viajero->getCorreo();
    //Generar pdf
    $html = $this->twig->render('viajeros/pcrPdf.html.twig', ['datosViajero' => $viajero,]);
    $pdf = $this->pdf->getOutputFromHtml($html);
    $message = (new TemplatedEmail())
    ->from('pcrresult@cicc.cu')
    ->to($email)
    ->addBcc('thaisalonso@infomed.sld.cu')
  //  ->bcc('lazaro.perez@cicc.cu','aixa.mira@cicc.cu','ilianed.puig@cicc.cu','ilianed.puig@infomed.sld.cu','elsa.rodriguez@cicc.cu','thais.alonso@cicc.cu','aime.mompie@cicc.cu'))
    ->subject('Resultados de PCR'.' '.$viajero->getNombre())
    ->htmlTemplate('viajeros/pcrEmail.html.twig')
    ->context([
      'viajero' => $viajero,
     ])
     ->attach($pdf, sprintf('resultado.pdf', date('Y-m-d')));
     ;

     try {
      $mailer->send($message);
      $viajero->setNotificado("si");
      $this->addFlash('success', 'Paciente notificado via correo correctamente');
      } catch (TransportExceptionInterface $e) {
        $this->addFlash('danger',$e);
      }
    
      $entityManager->persist($viajero);
      $entityManager->flush();
    
    return $this->redirectToRoute('viajeros');
  }
}
