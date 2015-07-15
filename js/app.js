var app = new angular.module('UCA',[]);

app.controller('SchoolUnivYearTrendController',['$scope', 'ucaService', function($scope,ucaService){
	var createChart = function(){
		var chart1 = new Highcharts.Chart({
	        chart: {
	            renderTo: 'container',
	            type: 'line'
	        },
	        title: {
	            text: 'Lowell High School to UC Davis'
	        },
	        xAxis: {
	        	title : {
	        		text: 'Year'
	        	},
	            categories: ucaService.getYears()
	        },
	        yAxis: {
	            title: {
	                text: 'Students'
	            }
	        },
	        series: [{
	            name: 'Applicants',
	            data: ucaService.getApplicants()
	        }, {
	            name: 'Admits',
	            data: ucaService.getAdmits()
	        }, {
	            name: 'Enrollees',
	            data: ucaService.getEnrollees()
	        }]
	    });
	};
	ucaService.getStudentCountBySchoolUniv().then(function(){
		createChart();
	});
}]);

app.service('ucaService',function($http, $q){
	var apiUrl = "http://localhost:8888/api/api.uca.php";
	var years_list = [];
	var applicants_list = [];
	var admits_list = [];
	var enrollees_list = [];

	this.getStudentCountBySchoolUniv = function(){
		var deferred = $q.defer();
		$http({
			method: 'GET',
			url: apiUrl,
			params: {
				query_type: 'studentcountbyschooluniv',
				school_name: "Lowell High School",
				city_name: 'San Francisco',
				univ_name:'Davis'
			}
		}).success(function(data){
			//return data;
			$.each(data, function(){
				years_list.push(this.year);
				applicants_list.push(parseInt(this.applicants,10));	//highcharts does not process numbers in string format
				admits_list.push(parseInt(this.admits,10));
				enrollees_list.push(parseInt(this.enrollees,10));
			});
			deferred.resolve(data);
			
		}).error(function(){
			deferred.reject('There was an error')
		});
		return deferred.promise;
	}

	this.getYears = function(){
		return years_list;
	}

	this.getApplicants = function(){
		return applicants_list;
	}

	this.getAdmits = function(){
		return admits_list;
	}

	this.getEnrollees = function(){
		return enrollees_list;
	}
});