<?php

namespace App\Http\Controllers;

use App\Detail;
use App\File;
use App\Project;
use Excel;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
/**
	需要先将vendor/maatwebsite/excel/src/config/excel.php中的heading，to_ascii改为false
*/
class ChartController extends Controller
{
	/**
		在旧excel中生成图表,并返回新的可下载的图表
	*/
	public function generateExcelChart(File $file){
		//set_time_limit(0);
		//获取文件在服务器上的地址
		$file_path = storage_path()."/files/".$file->title;
		$content = file_get_contents(storage_path()."/files/".$file->title);
		//获取当前文本编码格式
		$fileType = mb_detect_encoding($content , array('UTF-8','GBK','LATIN1','BIG5'));
		//获取excel对象
		$ori = Excel::load($file_path, function($reader){ 
	 	},$fileType); 
		//创建
	 	$excel = new \PHPExcel();
	 	$ori_object = $ori->get();

		//遍历所有的sheet
	 	for ($i=0; $i < count($ori_object); $i++) { 
	 		//设置chart输入位置，每次空16行
	 		$start_x = count($ori_object[$i])+6;
	 		$end_x = count($ori_object[$i])+16;
	 		//当前project
	 		$current_project = "";
	        $current_detail = "";
	        //设置当前sheet
	        $excel->createSheet();
	        $excel->setActiveSheetIndex($i);
	        $excel->getActiveSheet()->setTitle($ori_object[$i]->getTitle());
	        $objWorksheet = $excel->getActiveSheet();
	        //将月份横坐标和数据合并
	        $months = array('', '','', '1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月');
	        $month_data = $ori_object[$i]->toArray();
	        array_unshift($month_data,$months);
	        //设置Excel数据
	        $objWorksheet->fromArray(
		            $month_data
		    );
		    
	        //填充数据图
	        for ($j=0; $j < count($ori_object[$i])-2; $j=$j+3) {
	        	//检测是否为同一项目
	        	if($current_project!=$ori_object[$i][$j][0]){
	        		if($j==0){
	        			$current_detail = $ori_object[$i][$j][1];
	        			$current_project = $ori_object[$i][$j][0];
	        		}
	        		$current_project = $ori_object[$i][$j][0];
	        		$start_x = $start_x + 16;
	        		$end_x = $end_x + 16;
	        	}
	        	else{
	        		//检测是否为同一项目细则
	        		if($current_detail!=$ori_object[$i][$j][1]){
		        		$current_detail = $ori_object[$i][$j][1];
		        		$start_x = $start_x + 16;
		        		$end_x = $end_x + 16;
		        	}
	        	}
		        $dataseriesLabels1 = array(
		            new \PHPExcel_Chart_DataSeriesValues('String', 'Grafico!$C$'.(2+$j).':$C$'.(2+$j), NULL, 1), 
		            //目标
		        );
		        $dataseriesLabels2 = array(
		            new \PHPExcel_Chart_DataSeriesValues('String', 'Grafico!$C$'.(3+$j).':$C$'.(3+$j), NULL, 1), 
		            //实际
		        );
		        $dataseriesLabels3 = array(
		            new \PHPExcel_Chart_DataSeriesValues('String', 'Grafico!$C$'.(4+$j).':$C$'.(4+$j), NULL, 1), 
		            //达成率
		        );

		        $xAxisTickValues = array(
		            new \PHPExcel_Chart_DataSeriesValues('String', 'Grafico!$D$1:$O$1', NULL, 12), 
		            //一月到十二月
		        );

		        $dataSeriesValues1 = array(
		            new \PHPExcel_Chart_DataSeriesValues('Number', 'Grafico!$D$'.(2+$j).':$O$'.(2+$j), NULL, 12),
		        );

		        $dataSeriesValues2 = array(
		            new \PHPExcel_Chart_DataSeriesValues('Number', 'Grafico!$D$'.(3+$j).':$O$'.(3+$j), NULL, 12),
		        );

		        $dataSeriesValues3 = array(
		            new \PHPExcel_Chart_DataSeriesValues('Number', 'Grafico!$D$'.(4+$j).':$O$'.(4+$j), NULL, 12),
		        );

		        //建立数据序列
		        $series1 = new \PHPExcel_Chart_DataSeries(
		                \PHPExcel_Chart_DataSeries::TYPE_BARCHART, //图表类型
		                \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED, //条形类别
		                range(0, count($dataSeriesValues1) - 1), //画图顺序
		                $dataseriesLabels1, //底标
		                $xAxisTickValues, //类别
		                $dataSeriesValues1  //值
		        );
		        //横向读取数据
		        $series1->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_COL);

		        $series2 = new \PHPExcel_Chart_DataSeries(
		                \PHPExcel_Chart_DataSeries::TYPE_BARCHART, //图表类型
		                \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED, //条形类别
		                range(0, count($dataSeriesValues2) - 1), //画图顺序
		                $dataseriesLabels2, //底标
		                $xAxisTickValues, //类别
		                $dataSeriesValues2  //值
		        );
		        //横向读取数据
		        $series2->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_COL);

		        $series3 = new \PHPExcel_Chart_DataSeries(
		                \PHPExcel_Chart_DataSeries::TYPE_BARCHART, //图表类型
		                \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED, //条形类别
		                range(0, count($dataSeriesValues3) - 1), //画图顺序
		                $dataseriesLabels3, //底标
		                $xAxisTickValues, //类别
		                $dataSeriesValues3  //值
		        );
		        //横向读取数据
		        $series3->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_COL);

		        //设置画图
		        $plotarea = new \PHPExcel_Chart_PlotArea(NULL, array($series1, $series2, $series3));
		        //设置legend位置
		        $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

		        $chart_title = $current_project.'-'.$current_detail.'总体实际出货量变化图';
		        //设置图像title
		        
		        $title = new \PHPExcel_Chart_Title($chart_title);

		        //画图
		        $chart = new \PHPExcel_Chart(
		                'chart1', // name
		                $title, // title
		                $legend, // legend
		                $plotarea, // plotArea
		                // 'false', // plotVisibleOnly
		                0, // displayBlanksAs
		                NULL, // xAxisLabel
		                NULL            // yAxisLabel
		        );

		        //  Set the position where the chart should appear in the worksheet
		        $chart->setTopLeftPosition('A'.$start_x);
		        $chart->setBottomRightPosition('I'.$end_x);

		        //  Add the chart to the worksheet
		        $objWorksheet->addChart($chart);
		    }

	        $writer = new \PHPExcel_Writer_Excel2007($excel);
	        $writer->setIncludeCharts(TRUE);
	 	}
        //保存文件
	    $writer->save(storage_path()."/files/带图表_".$file->title);
	    
	    $file_path = storage_path().'/files/带图表_'.$file->title;
		return response()->download($file_path);
	} 

	/**
		生成线上柱状图
	*/
    public function showBarChart(File $file){
    	//找到project 例：供应商计划
    	$project = Project::where('file_id',$file->id)->get();
    	//记录所有的project
    	$all_projects = [];
    	for ($i=0; $i < count($project); $i++) { 
    		$project_temp = $project[$i];
    		//找到细则 例：海尔
	    	$details = Detail::where('project_id',$project_temp->id)->get();
	    	$current_name = "";
	    	//记录单个project的每个小项目
	    	$all_details=[];
	    	for ($j=0; $j < count($details)-11; $j = $j+12) { 
	    		//单个小项目从1-12月数据
	    		$detail = [];
	    		if($current_name != $details[$j]->name){
	    			$current_name = $details[$j]->name;
	    			$temp=[];
	    			for ($k=0; $k < 12; $k++) { 
				    	$temp['name'] = $project_temp->name." ".$details[($j+$k)]->name;
				    	$temp['id'] = $details[($j+$k)]->id;
				    	$temp['month'] = $details[($j+$k)]->month;
				    	//handle null value
				    	if($details[($j+$k)]->goal != null){
				    		$temp['goal'] = $details[($j+$k)]->goal;
				    	}
				    	else{
				    		$temp['goal'] = 0;
				    	}
				    	if($details[($j+$k)]->achieve != null){
				    		$temp['achieve'] = $details[($j+$k)]->achieve;
				    	}
				    	else{
				    		$temp['achieve'] = 0;
				    	}
				    	array_push($detail,(object) $temp);
				    }
	    		}	
	    		array_push($all_details,(object) $detail);
	    	}
	    	array_push($all_projects,(object) $all_details);
	    }
	   	return view('charts.barChart',compact('all_projects'));
    }

    /**
		生成线上柱状图
	*/
    public function showDataTable(File $file){

    	//找到project 例：供应商计划
    	$project = Project::where('file_id',$file->id)->get();
    	//记录所有的project
    	$all_projects = [];
    	for ($i=0; $i < count($project); $i++) { 
    		$project_temp = $project[$i];
    		//找到细则 例：海尔
	    	$details = Detail::where('project_id',$project_temp->id)->get();
	    	$current_name = "";
	    	//记录单个project的每个小项目
	    	$all_details=[];
	    	for ($j=0; $j < count($details)-11; $j = $j+12) { 
	    		//单个小项目从1-12月数据
	    		$detail = [];
	    		if($current_name != $details[$j]->name){
	    			$current_name = $details[$j]->name;
	    			$temp=[];
	    			for ($k=0; $k < 12; $k++) { 
	    				if(request("total")!=1){
		    				if((($k+1)<= request("to") && ($k+1)>= request("from")) || (request("to")==null && request("from")==null)) {
						    	$temp['name'] = $project_temp->name." ".$details[($j+$k)]->name;
						    	$temp['id'] = $details[($j+$k)]->id;
						    	$temp['month'] = $details[($j+$k)]->month;
						    	//handle null value
						    	if($details[($j+$k)]->goal != null){
						    		$temp['goal'] = $details[($j+$k)]->goal;
						    	}
						    	else{
						    		$temp['goal'] = 0;
						    	}
						    	if($details[($j+$k)]->achieve != null){
						    		$temp['achieve'] = $details[($j+$k)]->achieve;
						    	}
						    	else{
						    		$temp['achieve'] = 0;
						    	}
						    	array_push($detail,(object) $temp);
					    	}
					    }
					    else{
					    	if($k==0){
					    		$temp['name'] = $project_temp->name." ".$details[($j+$k)]->name;
						    	$temp['id'] = $details[($j+$k)]->id;
						    	$temp['month'] = '1-12月';
						    	$temp['goal']=0;
						    	$temp['achieve']=0;
					    	}
					    	
						    //handle null value
						    if($details[($j+$k)]->goal != null){
						    	$temp['goal'] = $temp['goal']+$details[($j+$k)]->goal;
						    }
						    else{
						    	$temp['goal'] = 0;
						    }
						    if($details[($j+$k)]->achieve != null){
						    	$temp['achieve'] = $temp['achieve']+$details[($j+$k)]->achieve;
						    }
						    else{
						    	$temp['achieve'] = 0;
						    }

						    if($k==11){
						    	array_push($detail,(object) $temp);
						    }
					    }
				    }
	    		}	
	    		array_push($all_details,(object) $detail);
	    	}
	    	array_push($all_projects,(object) $all_details);
	    }
	    $file_id = $file->id;
	    $file_name = $file->title;
	    $total = request('total');

	   	return view('datas.form',compact('all_projects','file_id','total','file_name'));
    }

    /**
    	上传excel图表到服务器
    */
    public function uploadExcel(Request $request){

    	$file = $request->file('myfile');

        if($file -> isValid()){
	        //Set file name with current time
	        $filename = $file->getClientOriginalName();
	        //$filename = $file->getClientOriginalName();
	        //Set directory to store file
	        $destinationPath = storage_path()."/files/"; // upload path
	        Input::file('myfile')->move($destinationPath, $filename); // uploading file to given path
	        File::create([
                'title'=>$filename,
                'used'=>false,
            ]);
	        return back()->with('success','上传成功');
	    }
	    else{
	    	return back()->with('error','上传失败');
	    }
	}


	/**
    	显示服务器上现有的excel图表
    */
	public function readExcel(){
		$files = File::all();
		return view('charts.showExcel',compact('files'));
	}

	/**
		更新excel数据到数据库
	*/
	public function updateExcelData(File $file){
		//文件第一次读取，将其直接放进数据库
		if($file->used == false){
			$file_path = storage_path()."/files/".$file->title;
			$table_name = "table_".$file->id;
			$content = file_get_contents(storage_path()."/files/".$file->title);
			$fileType = mb_detect_encoding($content , array('UTF-8','GBK','LATIN1','BIG5'));//获取当前文本编码格式

			//将excel 转化为object
			$sheet = Excel::load($file_path, function($reader){
	        },$fileType)->all(); 
			//遍历所有sheet
	        for ($i=0; $i < count($sheet); $i++) { 
	        	$title = $sheet[$i]->getTitle();
	        	$current_project = "";
	        	$current_project_id = "";
	        	$current_detail = "";
	        	$current_month = "";

	        	//遍历所有横行(除去第一行),每次读取三行：目标，实际，达成率
	        	for ($j=0; $j < count($sheet[$i])-2; $j=$j+3) {
	        		//发现新的project 创建新project
	        		//$sheet[$i][$j][0]: project name
	        		//$sheet[$i][$j][1]: detail name
	        		//$sheet[$i][$j+0][2]: 分别为目标，实际，达成率 
	        		//$sheet[$i][$j+1][2]:  
	        		//$sheet[$i][$j+2][2]: 
	        		//$sheet[$i][$j+*][3-14]: 1-12月数据  
	        		//dd($sheet[$i]);
	        		if($sheet[$i][$j][0]!= null && $current_project != $sheet[$i][$j][0]){
	        			$current_project = $sheet[$i][$j][0];
	        			$project = new Project;
	        			$project->name = $current_project;
	        			$project->file_id = $file->id;
	        			$project->save();
	        			$current_project_id = $project->id;

	        		}

	        		//创建新的detail数据，每次三行
	        		//第一行（目标）：goal
	        		//第二行（实际）：achieve
	        		//第三行（达成率）：rate
	        		$first_row = $sheet[$i][$j];
	        		$second_row = $sheet[$i][$j+1];
	        		$third_row = $sheet[$i][$j+2];
	        		//为每月创建数据细则
	        		for ($k=0; $k < 12; $k++) { 
	        			$detail = new Detail;
	        			$detail->project_id = $current_project_id;
	        			$detail->name = $sheet[$i][$j][1];
	        			$detail->month = ($k+1)."月";
	        			$detail->goal = $first_row[(3+$k)];
	        			$detail->achieve = $second_row[(3+$k)];
	        			$detail->rate = $third_row[(3+$k)];
	        			$detail->save();
	        		}	
	        	}
	        }
	        $file->used = true;
	        $file->save();
	        return back()->with('success','excel成功更新到数据库');;
	    }
	    //更新数据库信息
	    else{
	    	$file_path = storage_path()."/files/".$file->title;
			$table_name = "table_".$file->id;
			$content = file_get_contents(storage_path()."/files/".$file->title);
			$fileType = mb_detect_encoding($content , array('UTF-8','GBK','LATIN1','BIG5'));//获取当前文本编码格式

			//将excel 转化为object
			$sheet = Excel::load($file_path, function($reader){ 

	        },$fileType)->all(); 
			
			//遍历所有sheet
	        for ($i=0; $i < count($sheet); $i++) { 
	        	$title = $sheet[$i]->getTitle();
	        	$current_project = "";
	        	$current_project_id = "";
	        	$current_detail = "";
	        	$current_month = "";
	        	//遍历所有横行(除去第一行),每次读取三行：目标，实际，达成率
	        	for ($j=0; $j < count($sheet[$i])-2; $j=$j+3) {

	        		//发现新的project 创建新project
	        		//$sheet[$i][$j][0]: project name
	        		//$sheet[$i][$j][1]: detail name
	        		//$sheet[$i][$j+0][2]: 分别为目标，实际，达成率 
	        		//$sheet[$i][$j+1][2]:  
	        		//$sheet[$i][$j+2][2]: 
	        		//$sheet[$i][$j+*][3-14]: 1-12月数据 
	        		
	        		if($sheet[$i][$j][0]!= null && $current_project != $sheet[$i][$j][0]){
	        			$current_project = $sheet[$i][$j][0];
	        			$project = Project::where('file_id',$file->id)->where('name',$sheet[$i][$j][0])->first();
	        			$project->name = $current_project;
	        			//$project->save();
	        			$current_project_id = $project->id;
	        		}
	        		//创建新的detail数据，每次三行
	        		//第一行（目标）：goal
	        		//第二行（实际）：achieve
	        		//第三行（达成率）：rate
	        		$first_row = $sheet[$i][$j];
	        		$second_row = $sheet[$i][$j+1];
	        		$third_row = $sheet[$i][$j+2];

	        		//为每月创建数据细则
	        		for ($k=0; $k < 12; $k++) { 
	        			//dd($first_row[(1+$k)]);
	        			$detail = Detail::where('project_id',$current_project_id)->where('name',$first_row[1])->first();
	        			if($detail == null){
	        				dd($i.$j.$k);
	        			}
	        			else{
	        				$detail->name = $first_row[1];
		        			$detail->month = ($k+1)."月";
		        			$detail->goal = $first_row[(3+$k)];
		        			$detail->achieve = $second_row[(3+$k)];
		        			$detail->rate = $third_row[(3+$k)];
		        			$detail->update();
	        			}
	        			
	        		}	
	        	}
	        }
	        $file->used = true;
	        $file->update();
	        return back()->with('success','excel数据成功更新');;
	    }
	}

	/**
	* 删除数据库数据
	*/
	public function deleteExcelData(File $file){
		
		$project = Project::where('file_id',$file->id)->get();
		for ($i=0; $i < count($project); $i++) { 
			Detail::where('project_id',$project[$i]->id)->delete();
		}
		
		Project::where('file_id',$file->id)->delete();

		$file->used = false;
		$file->save();
		

		return back()->with('success','数据库数据删除成功');
	}

	/**
	*  删除图表文件
	*/
	public function deleteFile(File $file){
		\File::delete(storage_path().'/files/'.$file->title);
		$file->delete();
		return back();
	}

	/**
	* 下载excel表格
	*/
	public function downloadExcel(File $file){
		$file_path = storage_path().'/files/'.$file->title;
		return response()->download($file_path);
	}


}
