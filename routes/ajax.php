<?php

/*
|--------------------------------------------------------------------------
| Ajax Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('notification')->group(function () {
    Route::get('/read_flag/{notification}', 'NotificationController@set_read_status')->name('notificationReadStatus');
    Route::get('/clear', 'NotificationController@clear_notification')->name('clearNotification');
    Route::get('/get', 'NotificationController@get_notification')->name('getNotification');
});
Route::get('/user/list','UserController@list')->name('userList');
Route::get('/get/employees/{department?}','EmployeeController@getEmployees')->name('getEmployees');
Route::get('/get/email/{employee?}','EmployeeController@getEmail')->name('getOfficeEmail');
Route::get('/get/employee/detail/{employee?}','EmployeeController@getEmployeeDetail')->name('getEmployeeDetail');

Route::get('/get/users/{department?}','ManualAttendanceController@getUsers')->name('getUsers');
Route::get('/get/attendance','ManualAttendanceController@getAttendance')->name('getAttendance');

Route::prefix('employee')->group(function () {
    Route::get('/list','EmployeeController@list')->name('employeeList');
    Route::delete('/delete','EmployeeController@delete')->name('deleteEmployee');
    Route::post('/deactivate','EmployeeController@deactivateEmployee')->name('deactivateEmployee');
    Route::get('/profile/draft/list', 'EmployeeController@draft_list')->name('draftListAjax');
});
Route::prefix('domain')->group(function () {
    Route::get('/list','DomainController@list')->name('domainList');
    Route::post('/insert','DomainController@insert')->name('insertDomain');
    Route::post('/update/{domain}','DomainController@update')->name('updateDomain');
    Route::delete('/delete/{domain}','DomainController@delete')->name('deleteDomain');
});
Route::prefix('qualification')->group(function () {
    Route::get('/list','QualificationController@list')->name('qualificationList');
    Route::post('/insert','QualificationController@insert')->name('insertQualification');
    Route::post('/update','QualificationController@update')->name('updateQualification');
    Route::delete('/delete','QualificationController@delete')->name('deleteQualification');
});
Route::prefix('department')->group(function () {
    Route::get('/list','DepartmentController@list')->name('departmentList');
    Route::post('/insert','DepartmentController@insert')->name('insertDepartment');
    Route::post('/update','DepartmentController@update')->name('updateDepartment');
    Route::delete('/delete','DepartmentController@delete')->name('deleteDepartment');
    Route::get('/employee/list','DepartmentController@employeeList')->name('departmentEmployeeList');
});
Route::prefix('it')->group(function () {       
    Route::get('/software/list','ItController@softwareList')->name('softwareList');
    Route::get('/software/add','ItController@softwareCreate')->name('softwareCreate');
    Route::post('/software/submit','ItController@softwareSubmit')->name('softwareSubmit');
    Route::get('/software/edit/{software}','ItController@softwareEdit')->name('softwareEdit');
    Route::post('/software/submit','ItController@softwareSubmit')->name('softwareSubmit');
    Route::delete('/software/delete','ItController@softwaredelete')->name('softwareDelete');
    
});
//ticket-category
Route::prefix('ticket/category')->group(function () {
    Route::get('/list','TicketController@categoryList')->name('categoryList');
    Route::post('/submit','TicketController@categoryInsert')->name('categorySubmit');
    Route::post('/update','TicketController@categoryUpdate')->name('categoryUpdate');
    Route::delete('/delete','TicketController@categoryDelete')->name('categoryDelete');
});

Route::prefix('permission')->group(function () {
    Route::get('/list','AuthorizeController@permissionList')->name('permissionList');
    Route::post('/insert','AuthorizeController@insertPermission')->name('insertPermission');
    Route::post('/update','AuthorizeController@updatePermission')->name('updatePermission');
    Route::post('/delete','AuthorizeController@deletePermission')->name('deletePermission');
});
Route::prefix('role')->group(function () {
    Route::get('list','AuthorizeController@roleList')->name('roleList');
    Route::post('/insert','AuthorizeController@insertRole')->name('insertRole');
    Route::post('/update','AuthorizeController@updateRole')->name('updateRole');
    Route::post('/delete','AuthorizeController@deleteRole')->name('deleteRole');
});
Route::prefix('attendance')->group(function () {
    Route::get('/list/{employee}','EmployeeController@employeeAttendanceList')->name('employeeAttendanceList');
    Route::get('/list','AttendanceController@list')->name('attendanceList');
    Route::get('/list/{employee}/{month}','AttendanceController@monthlyDetail')->name('attendanceMonthlyRecord');
    Route::post('/update/{id}','AttendanceController@update')->name('updateAttendance');
});
    Route::get('/interviewee/pending/','IntervieweeController@list')->name('intervieweePendingList');

    //Leave routes
Route::prefix('leave')->group(function () {
    Route::get('/list','LeaveController@list')->name('leaveList');
    // Route::post('/leave/insert','LeaveController@insert')->name('insertLeave');
    // Route::post('/leave/update/{leave}','LeaveController@update')->name('updateLeave');
    Route::get('/leaveType', 'LeaveController@leaveType')->name('leaveType');

    Route::get('/approval/list', 'LeaveController@approvalList')->name('leaveApprovalList');
    // Route::post('/leave/approval/{leave}','LeaveController@updateApproval')->name('updateLeaveApproval');
});
    Route::get('/hardware/list','HardwareDetailController@list')->name('hardwareDetailList');
    Route::get('/hardware/allotedno/{entity?}','HardwareDetailController@getAllotedno')->name('hardwareAllotedno');

    // Route::post('update/office/email','EmployeeController@updateofficeEmail')->name('updateOfficeEmail');
    Route::post('update/alloted/office/email','EmployeeController@updateAllotedOfficeEmail')->name('updateAllotedOfficeEmail');
    Route::get('alloted/office/email/list','EmployeeController@allotedOfficeEmailList')->name('allotedOfficeEmailList');

Route::prefix('entity')->group(function () {
    Route::post('/list','EntityController@list')->name('entityList');
    Route::post('/add','EntityController@addEntity')->name('addEntity');
    Route::post('/update/{entity}','EntityController@updateEntity')->name('updateEntity');
    Route::delete('/delete/{entity}','EntityController@deleteEntity')->name('deleteEntity');
});

Route::prefix('equipment')->group(function () {
    Route::post('/list','EquipmentController@list')->name('equipmentList');
    Route::get('/check', 'EquipmentController@checkAllotedNumber')->name('equipmentNumberCheck');
    Route::get('/allot/{employee}/{equipment}','EquipmentController@allotEquipment')->name('allotEquipment');
    Route::get('/manufacturer', 'EquipmentController@autocomplete')->name('manufacturerAutocomplete');
    Route::get('/check/{employee}/{entity}','EquipmentController@checkCurrentEquipment')->name('equipmentCheck');
    Route::post('/request/allot/{entity}','EquipmentRequestController@allotEquipment')->name('alloteEquipments');
    Route::get('delete', 'EquipmentController@deleteEquipment')->name('deleteEquipment');
    Route::get('/get/employees/{entity?}', 'EquipmentRequestController@getEmployees')->name('employeesToAssign');
});
    Route::get('/specification/{specification}', 'EquipmentController@deleteSpecification')->name('deleteSpecification');
    Route::get('/problem/{problem}', 'EquipmentController@deleteProblem')->name('deleteProblem');
    Route::get('/repair/{repair}', 'EquipmentController@deleteRepair')->name('deleteRepair');
    //Role routes

Route::prefix('module')->group(function () {
    Route::get('/list','ModuleController@moduleList')->name('moduleList');
    Route::post('/insert','ModuleController@insertModule')->name('insertModule');
    Route::post('/update','ModuleController@updateModule')->name('updateModule');
    Route::post('/delete','ModuleController@deleteModule')->name('deleteModule');
});
    Route::get('/activity/list','ActivityController@list')->name('activityLogList');

    // entity request
    
Route::prefix('entity')->group(function () {
    // Route::post('/request/insert','EquipmentRequestController@insert')->name('insertEntityRequest');
    Route::post('/request/list','EquipmentRequestController@list')->name('entityRequestList');
    Route::post('/request/list/all','EquipmentRequestController@allList')->name('allEntityRequestList');
    Route::delete('/request/delete/{entity}','EquipmentRequestController@delete')->name('deleteEntityRequest');
    Route::post('/request/update/{entity}','EquipmentRequestController@update')->name('updateEntityRequest');
    Route::post('/request/alloted/no','EquipmentRequestController@getAllotedNo')->name('equipmentAllotedNo');
});

Route::prefix('manager')->group(function () {
    Route::get('/employee/list','ManagerController@list')->name('managerEmployeeList');
    Route::post('/department/equipment/list','ManagerController@lists')->name('managerDepartmentEquipmentList');
    Route::get('/attendance/list','ManagerController@attendanceList')->name('managerAttendanceList');
});
Route::prefix('trash')->group(function(){
    Route::post('/equipment/list','EquipmentController@trashList')->name('trashEquipmentList');
    Route::post('/equipment/forcedelete/','EquipmentController@forceDelete')->name('forceDeleteEquipment');
    Route::post('/equipment/restore/','EquipmentController@restore')->name('restoreEquipment');

    Route::post('/department/list','DepartmentController@trashList')->name('trashDepartmentList');
    Route::post('/department/delete/{department}','DepartmentController@forcedelete')->name('forceDeleteDepartment');
    Route::post('/department/restore','DepartmentController@restore')->name('restoreDepartment');

    Route::get('/role/list','AuthorizeController@trashRoleList')->name('trashRoleList');
    Route::delete('/role/delete/{role}','AuthorizeController@forcedeleteRole')->name('forceDeleteRole');
    Route::post('/role/restore/{role}','AuthorizeController@restoreRole')->name('restoreRole');

    Route::get('/permission/list','AuthorizeController@trashPermissionList')->name('trashPermissionList');
    Route::delete('/permission/delete/{permission}','AuthorizeController@forcedeletePermission')->name('forceDeletePermission');
    Route::post('/permission/restore/{permission}','AuthorizeController@restorePermission')->name('restorePermission');

    Route::post('/employee/list','EmployeeController@trashList')->name('trashEmployeeList');
    Route::post('/employee/restore','EmployeeController@restore')->name('restoreEmployee');
    Route::post('/employee/delete/{employee}','EmployeeController@forcedelete')->name('forceDeleteEmployee');

    Route::get('/module/list','ModuleController@trashList')->name('trashModuleList');
    Route::post('/module/restore/{module}','ModuleController@restore')->name('restoreModule');
    Route::delete('/module/delete/{module}','ModuleController@forcedelete')->name('forceDeleteModule');
});
    // document approval
    
Route::prefix('document')->group(function () {
    Route::post('/delete/{document}','EmployeeController@deleteDocument')->name('deleteDocument');
});

// send reminder

Route::get('profile/reminder/{employee}','EmployeeController@sendReminder')->name('sendReminder');

// quotation routes

Route::get('quotation/approval/{quotation}','ItController@sendForApproval')->name('sendForApproval');
Route::post('quotation/action','ItController@quotationAction')->name('quotationAction');
Route::get('quotation/delete/{quotation_id}','ITController@quotationDelete')->name('quotationDelete');
Route::get('quotation/detail/delete/{quotationDetail_id}','ITController@quotationDetailDelete')->name('deleteQuotationDetail');

// Item Request routes

Route::post('it/request/action','ItController@itemRequestAction')->name('itemRequestAction');

// leave action
Route::post('/leave/action','LeaveController@leaveAction')->name('leaveAction');
// leave cancellation
Route::get('/leave/cancel','LeaveController@cancelLeave')->name('leaveCancel');
// check label exists in stock detail
Route::get('/it/stock-detail/label','ItController@checkLabelExists')->name('checkLabelExists');

// set birthday read on
Route::get('/birthday/read-on/{id}','EmployeeController@setBirthdayReadOn')->name('setBirthdayReadOn');

// cancel equipment problem ticket
Route::get('equipment/problem-cancel/{id}','TicketController@cancelEquipmentProblem')->name('cancelEquipmentProblem');
Route::get('ticket/ticket-category','TicketController@getTicketCategories')->name('getTicketCategories');
// update dapertment

Route::post('hr/department/update', 'DepartmentController@managerUpdate')->name('hr.managerUpdate');

Route::get('check-session',function(){ return json_encode(['guest' => Auth::guest()]); });