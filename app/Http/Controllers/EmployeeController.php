<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Employee;
use App\Models\EmployeeHobby;
use App\Models\Hobby;
use Carbon\Carbon;
use File;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $hobbies  = Hobby::all();
        return view('employee.index', compact('categories', 'hobbies'));
    }

     /**
     * Display a listing of the resource.
     */

    public function getEmployee(Request $request)
    {
        $categories = Category::all();
        $hobbies  = Hobby::all();

        $start = $request->post("start");
        $limit = $request->post("length");
        $search = $request->post('search')['value'];
        $recordsTotal = Employee::count();
        $limit = ($limit == "-1") ? $recordsTotal : $limit;
        $employees = new Employee();
        if($search){
            $employees = $employees->where('name', 'like', "%$search%")
            ->orwhere('contact_no', 'like', "%$search%")
            ->orWhereHas('category',function($query) use($search){
                $query->where('name', 'like', "%$search%");
            })
            ->orWhereHas('hobbies.hobby',function($query) use($search){
                $query->where('name', 'like', "%$search%");
            });
        }
        $employees = $employees->skip($start)
        ->take($limit)
        ->orderBy('id', 'DESC')
        ->get();

        $data = [];
        foreach ($employees as $key=>$value){
            $hobbyData = $value->hobbies->pluck('hobby_id')->toArray();
            $profile_pic = '<img src='.asset('storage/profile/'.$value->profile_pic).' width=100 height=100><div class="mt-2 d-none profile-pic-cell-'.$value->id.'"><input type="file" class="form-control-file" id="profile-pic" name="profile_pic" accept="image/*"></div>';
            $button = '';
            $button .= '<button class="edit-employee btn btn-sm btn-success m-1" id="edit-employee" data-id="' . $value->id . '">
                <i class="bi bi-pen"></i>
                <span>Edit</span>
                </button>';

            $button .= '<button class="delete-employee btn btn-sm btn-danger m-1" data-id="' . $value->id . '">
                <i class="bi bi-trash"></i>
                <span>Delete</span>
                </button>';

            $data[] = [
                'id' => $key + 1,
                'select' => '<input type="checkbox" name="bulkdelete" value="' . $value->id . '">',
                'name' => '<span class="editable" data-field="name" data-id="' . $value->id . '">' . $value->name . '</span>',
                'contact_no' => '<span class="editable" data-field="contact_no" data-id="' . $value->id . '">' . $value->contact_no . '</span>',
                'hobby' => '<span class="editable-checkbox" data-field="hobby_ids" data-id="' . $value->id . '">' . $this->generateHobbyCheckboxes($value,$hobbies,$hobbyData) . '</span>',
                'category' => '<span class="editable-select" data-field="category_id" data-id="' . $value->id . '">' . $this->generateCategorySelect($value, $categories, $value->category_id) . '</span><span class="category-text-'.$value->id.'">'.$value->category->name.'</span>',
                'profile_pic' => $profile_pic,
                "action" => $button
            ];
        }
        return response()->json([
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsTotal),
            "data" => $data,
        ]);
    }

    /**
     * Generate hobby checkboxes
    */
    protected function generateHobbyCheckboxes($employee, $hobbies, $hobbyData) {
        $checkboxes = '';
        foreach ($hobbies as $hobby) {
            $checked = '';
            if(in_array($hobby->id, $hobbyData)){
                $checked = 'checked';
                $checkboxes .= '<input type="checkbox" class="hobby-checkbox-'.$employee->id.' d-none" name="hobby_ids[]" value="' . $hobby->id . '" '.$checked.'>'  . $hobby->name . '<br>';
            }else{
                $checkboxes .= '<input type="checkbox" class="hobby-checkbox-'.$employee->id.' d-none" name="hobby_ids[]" value="' . $hobby->id . '"><span class="hobby-text-'.$employee->id.' d-none">'  . $hobby->name . '</span><br>';
            }
        }
        return $checkboxes;
    }

    /**
     * Generate category select
     */
    protected function generateCategorySelect($employee, $categories, $selectedCategoryID) {
        $select = '<select class="form-control select-category-'.$employee->id.' d-none">';
        foreach ($categories as $category) {
            $selected = $category->id == $selectedCategoryID ? 'selected' : '';
            $select .= '<option value="' . $category->id . '" ' . $selected . '>' . $category->name . '</option>';
        }
        $select .= '</select>';
        return $select;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:10|unique:employees,contact_no',
            'category_id' => 'required|exists:categories,id',
            'hobby_ids' => 'required|array|min:1',
            'hobby_ids.*' => 'exists:hobbies,id',
            'profile_pic' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $employee = new Employee();
        $employee->name = $request->input('name');
        $employee->contact_no = $request->input('contact_no');
        $employee->category_id = $request->input('category_id');
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/profile',$filename);
            $employee->profile_pic = $filename;
        }
        $employee->save();
        if(!empty($request->input('hobby_ids'))){
            $employeeHobbyData = [];
            foreach($request->input('hobby_ids') as $value) {
                $employeeHobbyData[] = [
                    'employee_id' => $employee->id,
                    'hobby_id' => $value,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
            EmployeeHobby::insert($employeeHobbyData);
        }
    
        return response()->json(['message' => 'Employee added successfully']);
    }

    
    /**
     * Update a newly created resource in storage.
     */
    public function updateEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:10|unique:employees,contact_no,'.$request->id,
            'category_id' => 'required|exists:categories,id',
            'hobby_ids' => 'required|min:1',
            'profile_pic' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $employee = Employee::where('id', $request->id)->first();
        if(!empty($employee)){
            $employee->name = $request->input('name');
            $employee->contact_no = $request->input('contact_no');
            $employee->category_id = $request->input('category_id');
            if ($request->hasFile('profile_pic')) {
                $file = $request->file('profile_pic');
                $filename = time().'.'.$file->getClientOriginalExtension();
                $file->storeAs('public/profile',$filename);
                $employee->profile_pic = $filename;
            }
            $employee->save();
            $hobby_ids = explode(',',$request->input('hobby_ids'));
            if(!empty($hobby_ids)){
                EmployeeHobby::where('employee_id',$employee->id)->delete();
                $employeeHobbyData = [];
                foreach($hobby_ids as $value) {
                    $employeeHobbyData[] = [
                        'employee_id' => $employee->id,
                        'hobby_id' => $value,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                EmployeeHobby::insert($employeeHobbyData);
            }
            return response()->json(['message' => 'Employee updated successfully']);
        }
        return response()->json(['error' => 'Something went to wrong!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteEmployee(Request $request)
    {
        $data = [
            'status' => 2
        ];

        if(!empty($request->employeeId) || !empty($request->allids)){
            if($request->type == 'multiple'){
                EmployeeHobby::whereIn('employee_id', $request->allids)->delete();
                $employees = Employee::whereIn('id', $request->allids)->get();
                foreach($employees as $employee){
                    $image_path = public_path().'/storage/profile/'.$employee->profile_pic;
                    if(File::exists($image_path)) {
                     File::delete($image_path);
                    }
                    $employee->delete();
                }
            }else{
                $employee = Employee::where('id', $request->employeeId)->first();
                if(!empty($employee)){
                    EmployeeHobby::where('employee_id', $employee->id)->delete();
                    $image_path = public_path().'/storage/profile/'.$employee->profile_pic;
                    if(File::exists($image_path)) {
                     File::delete($image_path);
                    }
                    $employee->delete();
                }
            }
            $data = [
                'status' => 1
            ];
        }
        return $data;
    }
}
