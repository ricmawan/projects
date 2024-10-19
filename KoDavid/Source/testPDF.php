<?php 
require ("./assets/lib/fpdf17/fpdf.php");

  //customer and invoice details
$info=[
	"customer"=>"Ram Kumar",
	"address"=>"4th cross,Car Street,",
	"city"=>"Salem 636204.",
	"invoice_no"=>"1000001",
	"invoice_date"=>"30-11-2021",
	"total_amt"=>"5200.00",
	"words"=>"Rupees Five Thousand Two Hundred Only",
];


  //invoice Products
$products_info=[
	[
		"name"=>"UPS",
		"price"=>"3000.00",
		"qty"=>1,
		"total"=>"3000.00"
	],
	[
		"name"=>"UPS",
		"price"=>"3000.00",
		"qty"=>1,
		"total"=>"3000.00"
	],
	[
		"name"=>"UPS",
		"price"=>"3000.00",
		"qty"=>1,
		"total"=>"3000.00"
	],
	[
		"name"=>"UPS",
		"price"=>"3000.00",
		"qty"=>1,
		"total"=>"3000.00"
	],
	[
		"name"=>"UPS",
		"price"=>"3000.00",
		"qty"=>1,
		"total"=>"3000.00"
	],
	[
		"name"=>"LEMARI KABINET NAPOLY CABRO -  R21X.C2K.UBIT (PUMA) KUNCI",
		"price"=>"500.00",
		"qty"=>2,
		"total"=>"1000.00"
	],
	[
		"name"=>"Mouse",
		"price"=>"400.00",
		"qty"=>3,
		"total"=>"1200.00"
	],
	[
		"name"=>"Mouse",
		"price"=>"400.00",
		"qty"=>3,
		"total"=>"1200.00"
	]
	,
	[
		"name"=>"Mouse",
		"price"=>"400.00",
		"qty"=>3,
		"total"=>"1200.00"
	],
	[
		"name"=>"Mouse",
		"price"=>"400.00",
		"qty"=>3,
		"total"=>"1200.00"
	],
	[
		"name"=>"Mouse",
		"price"=>"400.00",
		"qty"=>3,
		"total"=>"1200.00"
	]

];

class PDF extends FPDF
{
	function Header($info){

      //Display Company Info
		$this->SetFont('Arial','B',12);
		$this->Cell(50,10,"Toko Mulya Jaya",0,1);
		$this->SetFont('Arial','',12);
		$this->Cell(50,7,"Jl Merdeka Utara GG Pelita II",0,1);
		$this->Cell(50,7,"Ciledug - Jawa Barat",0,1);
		$this->Cell(50,7,"Telp : 087777000",0,1);

       //Display Invoice no
		$this->SetY(15);
		$this->SetX(-80);
		$this->Cell(50,7,"No Nota     : ".$info["invoice_no"]);

      //Display Invoice date
		$this->SetY(22);
		$this->SetX(-80);
		$this->Cell(50,7,"Tanggal     : ".$info["invoice_date"]);

      //Display Invoice date
		$this->SetY(29);
		$this->SetX(-80);
		$this->Cell(50,7,"Pelanggan : ".$info["invoice_date"]);


      //Display Horizontal line
		$this->Line(5,37,205,37);

		$this->SetY(40);
		$this->SetX(5);
		$this->SetFont('Arial','B',10);
		$this->Cell(130,9,"Nama Barang",1,0);
		$this->Cell(25,9,"Harga",1,0,"C");
		$this->Cell(15,9,"Qty",1,0,"C");
		$this->Cell(30,9,"Sub Total",1,1,"C");

	}

	function body($info,$products_info){
		$this->SetFont('Arial','',9);      
      //Display table product rows
		$i = 0;
		$row_each_page = 10;
		foreach($products_info as $row){
			$i++;
			if(fmod($i, $row_each_page) == 0 && count($products_info) > $row_each_page) {

				$this->Cell(130,6,$i." break ".$row["name"],"LRB",0);
				$this->Cell(25,6,$row["price"],"RB",0,"R");
				$this->Cell(15,6,$row["qty"],"RB",0,"C");
				$this->Cell(30,6,$row["total"],"RB",1,"R");

				if($i != count($products_info)  ) {
					$this->Cell(130,6,"","",0);
					$this->Cell(25,6,"","",0);
					$this->Cell(15,6,"","",0);
					$this->Cell(30,6,"","",1);

					$this->Cell(130,6,"","",0);
					$this->Cell(25,6,"","",0);
					$this->Cell(15,6,"","",0);
					$this->Cell(30,6,"","",1);

					$this->Cell(130,6,"","",0);
					$this->Cell(25,6,"","",0);
					$this->Cell(15,6,"","",0);
					$this->Cell(30,6,"","",1);
				}
			}
			else {
				$this->Cell(130,6,fmod($i, $row_each_page)." ".$row["name"],"LRB",0);
				$this->Cell(25,6,$row["price"],"RB",0,"R");
				$this->Cell(15,6,$row["qty"],"RB",0,"C");
				$this->Cell(30,6,$row["total"],"RB",1,"R");
			}
		}

      //blank row
		if(fmod(count($products_info), $row_each_page) > 0) {
			for($j=0;$j<($row_each_page-fmod(count($products_info), $row_each_page));$j++)
			{
				//if(count($products_info) > 10) {
					$this->Cell(130,6,"","LR",0);
					$this->Cell(25,6,"","R",0,"R");
					$this->Cell(15,6,"","R",0,"C");
					$this->Cell(30,6,"","R",1,"R");
				//}
			}
		}
      //Display table empty rows
     /* for($j=0;$j<8-fmod($i, 10);$j++)
      {
      	if(count($products_info) > 10) {
	        $this->Cell(130,9,"","LR",0);
	        $this->Cell(25,9,"","R",0,"R");
	        $this->Cell(15,9,"","R",0,"C");
	        $this->Cell(30,9,"","R",1,"R");
	       }
	     }*/
      //Display table total row
	     $this->SetFont('Arial','B',10);
	     $this->Cell(170,6,"Total",1,0,"R");
	     $this->Cell(30,6,$info["total_amt"],1,1,"R");

	     $this->Cell(170,6,"Diskon",1,0,"R");
	     $this->Cell(30,6,"2000.00",1,1,"R");
	     
	     $this->Cell(170,6,"Grand Total",1,0,"R");
	     $this->Cell(30,6,"50000.00",1,1,"R");




	   }
	   function Footer(){

      /*//set footer position
      $this->SetY(-50);
      $this->SetFont('Arial','B',12);
      $this->Cell(0,10,"for ABC COMPUTERS",0,1,"R");
      $this->Ln(15);
      $this->SetFont('Arial','',12);
      $this->Cell(0,10,"Authorized Signature",0,1,"R");
      $this->SetFont('Arial','',10);
      
      //Display Footer Text
      $this->Cell(0,10,"This is a computer generated invoice",0,1,"C");*/
      
    }
    
  }
  //Create A4 Page with Portrait 
  $pdf=new PDF("L","mm","A5");
  $pdf->SetMargins(5, 5);
  $pdf->AddPage();
  $pdf->body($info,$products_info);
  $pdf->Output();
?>