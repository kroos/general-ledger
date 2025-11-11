@extends('layouts.app')

@section('content')
<div class="col-sm-12">

	<div class="col-sm-12 row justify-content-center align-items-center my-2 m-0 border border-success">
		<h1 class="text-center animate__animated animate__bounce">An animated element</h1>
	</div>

	<div class="col-sm-12 row text-center align-items-center my-2 m-0 border border-success">
		<div class="tw">
			<p class="text-3xl font-bold underline">Hello tailwindcss</p>
			<button class="btn btn-primary">Bootstrap Button</button>
		</div>
		<p>If you want to use tailwindcss class, please wrap it with "tw" class. This has been made to resolve conflicts between bootstrap and tailwindcss</p>
		<p>{{ __('<div class="tw"><p class="text-3xl font-bold underline">Hello tailwindcss</p></div>') }}</p>
	</div>

	<div class="col-sm-12 row justify-content-center align-items-center my-1 m-0 border border-success">
		<p class="">Placeholder text to demonstrate some <a href="#" data-toggle="tooltip" data-bs-title="Default tooltip">inline links</a> with tooltips. This is now just filler, no killer. Content placed here just to mimic the presence of <a href="#" data-toggle="tooltip" data-bs-title="Another tooltip">real text</a>. And all that just to give you an idea of how tooltips would look when used in real-world situations. So hopefully you've now seen how <a href="#" data-toggle="tooltip" data-bs-title="Another one here too">these tooltips on links</a> can work in practice, once you use them on <a href="#" data-toggle="tooltip" data-bs-title="The last tip!">your own</a> site or project.
		</p>
	</div>

	<div class="col-sm-12 row justify-content-center align-items-center my-2 m-0 border border-success">
		<div class="row col-sm-6 border border-primary">
			<label for="select2" class="col-form-label col-sm-4">Select 2:</label>
			<div class="col-sm-8 my-auto">
				<select name="select2" id="select2" class="form-select form-select-sm col-sm-8" placeholder="Please choose">
					<option value="">Please choose</option>
					<option value="1">Pick 1</option>
					<option value="2">Pick 2</option>
				</select>
			</div>
		</div>
	</div>

	<div class="col-sm-12 row justify-content-center align-items-center my-2 m-0 border border-success">
		<div class="row col-sm-6 border border-primary">
			<label for="dp" class="col-form-label col-sm-4">jQuery-ui Datepicker:</label>
			<div class="col-sm-8 my-auto">
				<input type="text" id="dp" name="datepicker" class="form-control form-control-sm">
			</div>
		</div>
		<figure class="text-start">
			<blockquote class="blockquote">
				<p>
				$("#dp").jqueryuiDatepicker({</br>
			dateFormat: 'yy-mm-dd',</br>
		});</br>
	</p>
</blockquote>
<figcaption class="blockquote-footer">
	For all jQuery-UI method, u can prefix it with "jquery", this to avoid a conflicts between bootstrap method and jQuery-UI method.
</figcaption>
</figure>
</div>

<div class="col-sm-12 row justify-content-center align-items-center my-2 m-0 border border-success">
	<div class="col-sm-4 my-auto">
		<button id="button1" class="m-1 btn btn-primary"><i class="fa-regular fa-user fa-beat"></i> Primary button</button>
		<button id="button2" class="m-1 btn btn-secondary"><i class="fa-solid fa-bomb fa-beat"></i> secondary button</button>
		<button id="button3" class="m-1 btn btn-outline-primary"><i class="bi bi-airplane-engines"></i> third button</button>
		<button id="button4" class="m-1 btn btn-outline-primary"><span class="mdi mdi-ab-testing"></span> fourth button</button>
	</div>
</div>

<div class="col-sm-12 row justify-content-center align-items-center my-2 m-0 border border-success">
	<h2>1 And 2 Tier Dynamic Inputs (with Form)</h2>

	<form id="myForm" action="{{ route('welcome') }}" method="post">
		@csrf
		@include('_form')
	</form>
</div>

<div class="col-sm-12 row justify-content-center align-items-center my-2 m-0 border border-success">
	<table id="table_id" class="table table-sm table-hover">
		<thead>
			<tr>
				<th>Name</th>
				<th>Position</th>
				<th>Office</th>
				<th>Age</th>
				<th>Start date</th>
				<th>Salary</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Tiger Nixon</td>
				<td>System Architect</td>
				<td>Edinburgh</td>
				<td>61</td>
				<td>25 Apr 2011</td>
				<td>$320,800</td>
			</tr>
			<tr>
				<td>Garrett Winters</td>
				<td>Accountant</td>
				<td>Tokyo</td>
				<td>63</td>
				<td>25 Jul 2011</td>
				<td>$170,750</td>
			</tr>
			<tr>
				<td>Ashton Cox</td>
				<td>Junior Technical Author</td>
				<td>San Francisco</td>
				<td>66</td>
				<td>12 Jan 2009</td>
				<td>$86,000</td>
			</tr>
			<tr>
				<td>Cedric Kelly</td>
				<td>Senior Javascript Developer</td>
				<td>Edinburgh</td>
				<td>22</td>
				<td>29 Mar 2012</td>
				<td>$433,060</td>
			</tr>
			<tr>
				<td>Airi Satou</td>
				<td>Accountant</td>
				<td>Tokyo</td>
				<td>33</td>
				<td>28 Nov 2008</td>
				<td>$162,700</td>
			</tr>
			<tr>
				<td>Brielle Williamson</td>
				<td>Integration Specialist</td>
				<td>New York</td>
				<td>61</td>
				<td>2 Dec 2012</td>
				<td>$372,000</td>
			</tr>
			<tr>
				<td>Herrod Chandler</td>
				<td>Sales Assistant</td>
				<td>San Francisco</td>
				<td>59</td>
				<td>6 Aug 2012</td>
				<td>$137,500</td>
			</tr>
			<tr>
				<td>Rhona Davidson</td>
				<td>Integration Specialist</td>
				<td>Tokyo</td>
				<td>55</td>
				<td>14 Oct 2010</td>
				<td>$327,900</td>
			</tr>
			<tr>
				<td>Colleen Hurst</td>
				<td>Javascript Developer</td>
				<td>San Francisco</td>
				<td>39</td>
				<td>15 Sep 2009</td>
				<td>$205,500</td>
			</tr>
			<tr>
				<td>Sonya Frost</td>
				<td>Software Engineer</td>
				<td>Edinburgh</td>
				<td>23</td>
				<td>13 Dec 2008</td>
				<td>$103,600</td>
			</tr>
			<tr>
				<td>Jena Gaines</td>
				<td>Office Manager</td>
				<td>London</td>
				<td>30</td>
				<td>19 Dec 2008</td>
				<td>$90,560</td>
			</tr>
			<tr>
				<td>Quinn Flynn</td>
				<td>Support Lead</td>
				<td>Edinburgh</td>
				<td>22</td>
				<td>3 Mar 2013</td>
				<td>$342,000</td>
			</tr>
			<tr>
				<td>Charde Marshall</td>
				<td>Regional Director</td>
				<td>San Francisco</td>
				<td>36</td>
				<td>16 Oct 2008</td>
				<td>$470,600</td>
			</tr>
			<tr>
				<td>Haley Kennedy</td>
				<td>Senior Marketing Designer</td>
				<td>London</td>
				<td>43</td>
				<td>18 Dec 2012</td>
				<td>$313,500</td>
			</tr>
			<tr>
				<td>Tatyana Fitzpatrick</td>
				<td>Regional Director</td>
				<td>London</td>
				<td>19</td>
				<td>17 Mar 2010</td>
				<td>$385,750</td>
			</tr>
		</tbody>
	</table>
</div>

<div class="col-sm-12 row justify-content-center align-items-center my-2 m-0 border border-success">
	<canvas id="myChart"></canvas>
</div>

<div class="col-sm-12 row justify-content-center align-items-center my-2 m-0 border border-success">
	<div id="calendar"></div>
</div>
</div>
@endsection

@section('js')
///////////////////////////////////////////////////////////////////////////////////////////
// tooltip
$(document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip();
});

///////////////////////////////////////////////////////////////////////////////////////////
$('#button1').click(function(){
	alert("Thanks");
});

///////////////////////////////////////////////////////////////////////////////////////////
$('#button2').click(function(){
	swal.fire('Title', 'message', 'info');
});

///////////////////////////////////////////////////////////////////////////////////////////
console.log('test');

///////////////////////////////////////////////////////////////////////////////////////////
$('#select2').select2({
	theme: 'bootstrap-5',
});

///////////////////////////////////////////////////////////////////////////////////////////
console.log(moment().format('D MMMM YYYY'));

///////////////////////////////////////////////////////////////////////////////////////////
$("#dp").jqueryuiDatepicker({
	dateFormat: 'yy-mm-dd',
});

///////////////////////////////////////////////////////////////////////////////////////////
DataTable.datetime('D MMM YYYY');
$('#table_id').DataTable({
	'lengthMenu': [ [30, 60, 100, -1], [30, 60, 100, 'All'] ],
	'columnDefs': [
	{ type: 'date', 'targets': [4] },
	],
	'order': [[ 0, 'desc' ]],
	'responsive': true,
	'autoWidth': false,
	// 'fixedHeader': true,
	'dom': 'Bfrtip',
});

///////////////////////////////////////////////////////////////////////////////////////////
// Experiences (fieldName "experiences")
$("#experience_wrap").remAddRow({
	addBtn: "#experience_add",
	maxFields: 3,
	removeSelector: ".exp_remove",
	fieldName: "experiences",
	rowIdPrefix: "exp",
	rowTemplate: (i, name) => `
	<div class="col-sm-12 row g-3 m-1" id="exp_${i}">
		<div class="form-floating col-sm-4 @error('experiences.*.name') is-invalid @enderror">
			<input type="text" name="${name}[${i}][name]" id="name_${i}" class="form-control @error('experiences.*.name') is-invalid @enderror">
			<label for="name_${i}" class="form-col-label">Name :</label>
			@error('experiences.*.name')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
		<div class="form-floating col-sm-4 @error('experiences.*.id') is-invalid @enderror">
			<input type="text" name="${name}[${i}][id]" id="id_${i}" class="form-control @error('experiences.*.id') is-invalid @enderror">
			<label for="id_${i}" class="form-col-label">ID :</label>
			@error('experiences.*.id')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
		<div class="col-sm-1">
			<button class="btn btn-sm btn-outline-danger exp_remove" data-id="${i}"><i class="fa-solid fa-xmark fa-beat"></i></button>
		</div>
	</div>
	`,
	onAdd: (i, row) => {
		console.log("Experience added:", `exp_${i}`, row);
	},
	onRemove: (i) => {
		console.log("Experience removed:", `exp_${i}`);
	},
});

///////////////////////////////////////////////////////////////////////////////////////////
// 2 tier dynamic input
$("#skills_wrap").remAddRow({
	addBtn: "#skills_add",
	maxFields: 3,
	fieldName: "skills",
	rowIdPrefix: "skill",
	removeSelector: ".skill_remove",
	rowTemplate: (i, name) => `
	<div class="col-sm-12 m-1 row border border-primary rounded" id="skill_${i}">
		<div class="col-sm-12 m-0 row">
			<label for="name_${i}" class="form-col-label col-sm-3">Name #${i+1}</label>
			<div class="col-sm-9 row">
				<div class="col-sm-10 my-auto">
					<input type="text" name="${name}[${i}][name]" value="{{ old('skills.*.name', @$variable->name) }}" id="name_${i}" class="form-control form-control-sm @error('skills.*.name') is-invalid @enderror" placeholder="Name ${i+1}">
					@error('skills.*.name')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
					@enderror
				</div>
			</div>
		</div>
		<div class="col-sm-12 m-0 row">
			<label for="sk_${i}" class="form-col-label col-sm-3">Skill #${i+1}</label>
			<div class="col-sm-9 row my-auto">
				<div class="col-sm-10 m-0">
					<input type="text" name="${name}[${i}][skill]" value="{{ old('skills.*.skill', @$variable->skill) }}" id="sk_${i}" class="form-control form-control-sm @error('skills.*.skill') is-invalid @enderror" placeholder="Skill ${i+1}">
					@error('skills.*.skill')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
					@enderror
				</div>
				<div class="col-sm-1 m-1">
					<button class="btn btn-sm btn-outline-danger skill_remove" data-id="${i}">
						<i class="fa-regular fa-trash-can fa-beat"></i>
					</button>
				</div>
			</div>
		</div>

		<!-- Sub-skills wrapper -->
		<div class="col-sm-9 offset-sm-3 my-1 border border-primary-subtle rounded">
			<div id="subskill_wrap_${i}">
			</div>
			<button type="button" id="subskill_add_${i}" class="m-1 btn btn-sm btn-primary">+ Add Sub-skill</button>
		</div>

	</div>
	`,
	onAdd: (i, $row1) => {
		console.log("Skill added:", "skill_"+i, $row1);

		// initialize sub-skills for this skill
		$(`#subskill_wrap_${i}`).remAddRow({
			addBtn: `#subskill_add_${i}`,
			maxFields: 5,
			fieldName: `skills[${i}][subskills]`,
			rowIdPrefix: `subskill_${i}`,
			rowTemplate: (j, name) => `
			<div class="col-sm-12 m-1 row border border-info-subtle rounded" id="subskill_${i}_${j}">
				<div class="col-sm-12 m-1 row">
					<label for="sbsk_${j}" class="form-col-label col-sm-2">Sub-skill #${j+1}</label>
					<div class="col-sm-8 my-auto">
						<input type="text" name="${name}[${j}][subskill]" value="{{ old('skills.*.subskills.*.subskill', @$variable->subskill) }}" id="sbsk_${j}" class="form-control form-control-sm @error('skills.*.subskills.*.subskill') is-invalid @enderror" placeholder="Sub-skill ${j+1}">
						@error('skills.*.subskills.*.subskill')
						<div class="invalid-feedback">
							{{ $message }}
						</div>
						@enderror
					</div>
				</div>

				<div class="col-sm-12 m-1 row">
					<label for="sbsky_${j}" class="form-col-label col-sm-2">Years #${j+1}</label>
					<div class="col-sm-8 my-auto">
						<input type="text" name="${name}[${j}][years]" value="{{ old('skills.*.subskills.*.years', @$variable->years) }}" id="sbsky_${j}" class="form-control form-control-sm @error('skills.*.subskills.*.years') is-invalid @enderror" placeholder="Years ${j+1}">
						@error('skills.*.subskills.*.years')
						<div class="invalid-feedback">
							{{ $message }}
						</div>
						@enderror
					</div>
					<div class="col-sm-1">
						<button class="btn btn-sm btn-outline-danger subskill_remove" data-id="${j}">
							<i class="fa-regular fa-trash-can fa-beat"></i>
						</button>
					</div>
				</div>
			</div>
			`,
			removeSelector: ".subskill_remove",
			onAdd: (j, $row2) => {
				console.log("Sub-skill added:", `skill_${i}_${j}`, $row2);
			},
			onRemove: (j) => {
				console.log("Sub-skill removed:", `skill_${i}_${j}`);
			}
		});
	},
	onRemove: (i) => {
		console.log("Skill removed:", "skill_"+i);
	}
});

///////////////////////////////////////////////////////////////////////////////////////////
// Countries (fieldName "countries")
let selectedStates = []; // globally track selected state IDs
$("#countries_wrap").remAddRow({
	addBtn: "#countries_add",
	maxFields: 3,
	removeSelector: ".country_remove",
	fieldName: "countries",
	rowIdPrefix: "ctry",
	rowTemplate: (i, name) => `
		<div class="col-sm-12 row m-0 my-1 border border-warning-subtle rounded" id="ctry_${i}">
			<div class="col-sm-10 row m-0 my-1">
				<label for="country_${i}" class="col-sm-2 form-col-label">Country : </label>
				<div class="col-sm-10">
					<select name="${name}[${i}][country_id]" id="country_${i}" class="form-select form-select-sm @error('countries.*.country_id') is-invalid @enderror">
						<option value="">Please choose</option>
					</select>
					@error('countries.*.country_id')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
					@enderror
				</div>
			</div>
			<div class="col-sm-10 row m-0 my-1">
				<label for="state_${i}" class="col-sm-2 form-col-label">State : </label>
				<div class="col-sm-9 my-auto">
					<select name="${name}[${i}][state_id]" id="state_${i}" class="form-select form-select-sm @error('countries.*.state_id') is-invalid @enderror">
						<option value="">Please choose</option>
					</select>
					@error('countries.*.state_id')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
					@enderror
				</div>
				<div class="col-sm-1">
					<button class="btn btn-sm btn-outline-danger country_remove" data-id="${i}">
						<i class="fa-regular fa-trash-can fa-beat"></i>
					</button>
				</div>
			</div>
		</div>
	`,
	onAdd: (i, row) => {
		console.log("Country added:", `ctry_${i}`, row);


		const $country = $('#country_' + i);
		const $state = $('#state_' + i);

		// Initialize country select2
		$country.select2({
			placeholder: 'Select Country',
			width: '100%',
			allowClear: true,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('countries') }}',
				dataType: 'json',
				processResults: data => ({ results: data })
			}
		});

		// Initialize empty state select2
		$state.select2({
			placeholder: 'Select State',
			width: '100%',
			allowClear: true,
			closeOnSelect: true,
		});

		// When country changes, reload states dynamically
		$country.on('change', function () {
			const countryId = $(this).val();
			$state.val(null).trigger('change');
			if (countryId) {
				$state.select2({
					placeholder: 'Select State',
					width: '100%',
					allowClear: true,
					closeOnSelect: true,
					ajax: {
						url: `{{ url('api/states') }}/${countryId}`,
						dataType: 'json',
						processResults: data => ({
							results: data.filter(item => !selectedStates.includes(String(item.id)))
						})
					}
				});
			}
		});

		// Track selected state
		let previousState = null;

		$state.on('select2:select', function (e) {
			const stateId = e.params.data.id;
			// Remove previous (if any) and add new
			if (previousState) {
				selectedStates = selectedStates.filter(id => id !== String(previousState));
			}
			selectedStates.push(String(stateId));
			previousState = stateId;
			// Refresh all state dropdowns so they re-filter
			refreshAllStateDropdowns();
		});

		$state.on('select2:clear', function () {
			// If cleared manually
			if (previousState) {
				selectedStates = selectedStates.filter(id => id !== String(previousState));
				previousState = null;
				refreshAllStateDropdowns();
			}
		});

		// Helper: reapply filters on all state select2s
		function refreshAllStateDropdowns() {
			$('#countries_wrap select[id^="state_"]').each(function () {
				const sel = $(this);
				const countryId = sel.closest('.row').find('select[id^="country_"]').val();
				if (countryId) {
					sel.select2({
						placeholder: 'Select State',
						width: '100%',
						allowClear: true,
						closeOnSelect: true,
						ajax: {
							url: `{{ url('api/states') }}/${countryId}`,
							dataType: 'json',
							processResults: data => ({
								results: data.filter(item => !selectedStates.includes(String(item.id)) || sel.val() === String(item.id))
							})
						}
					});
				}
			});
		}

	},
	onRemove: (i) => {
		console.log("Country removed:", `ctry_${i}`);
		// When a row is removed, remove its selected state from tracking
		const stateVal = $(`#state_${i}`).val();
		if (stateVal) {
			selectedStates = selectedStates.filter(id => id !== String(stateVal));
		}
	},
});

///////////////////////////////////////////////////////////////////////////////////////////
// restore after fail form process
$(function () {
@php
    $items = @$variable?->hasmanyModel()?->get(['column']);
    $itemsArray = $items?->toArray();
    $oldItemsValue = old('items', $itemsArray);
@endphp

	const oldSkills = @json(old('skills', @$variable?->hasmanyModel()?->get(['column'])?->toArray() ));
	const oldExperiences = @json(old('experiences', @$variable?->hasmanyModel()?->get(['column'])?->toArray() ));
	const oldCountries = @json(old('countries', @$variable?->hasmanyModel()?->get(['column'])?->toArray() ));

	// === Restore old SKILLS ===
	if (oldSkills.length > 0) {
		oldSkills.forEach(function (skill, i) {
			$("#skills_add").trigger('click'); // simulate add skill
			const $skill = $("#skills_wrap").children().eq(i);

			// Fill skill name + main skill
			$skill.find(`input[name="skills[${i}][name]"]`).val(skill.name || '');
			$skill.find(`input[name="skills[${i}][skill]"]`).val(skill.skill || '');

			// === Restore SUB-SKILLS ===
			if (skill.subskills && skill.subskills.length > 0) {
				skill.subskills.forEach(function (sub, j) {
					$(`#subskill_add_${i}`).trigger('click'); // simulate add sub-skill
					const $sub = $(`#subskill_wrap_${i}`).children().eq(j);
					$sub.find(`input[name="skills[${i}][subskills][${j}][subskill]"]`).val(sub.subskill || '');
					$sub.find(`input[name="skills[${i}][subskills][${j}][years]"]`).val(sub.years || '');
				});
			}
		});
	}

	// === Restore old EXPERIENCES ===
	if (oldExperiences.length > 0) {
		oldExperiences.forEach(function (exp, i) {
			$("#experience_add").trigger('click'); // simulate add experience
			const $exp = $("#experience_wrap").children().eq(i);
			$exp.find(`input[name="experiences[${i}][name]"]`).val(exp.name || '');
			$exp.find(`input[name="experiences[${i}][id]"]`).val(exp.id || '');
		});
	}

	// === Restore old COUNTRIES ===
	if (oldCountries.length > 0) {

		oldCountries.forEach(function (ctry, i) {
			// Add row dynamically
			$("#countries_add").trigger('click');

			// Grab the newly added row
			const $row = $("#countries_wrap").children().eq(i);

			const $country = $row.find(`select[name="countries[${i}][country_id]"]`);
			const $state = $row.find(`select[name="countries[${i}][state_id]"]`);

			// --- Restore Country ---
			if (ctry.country_id) {
				// Create option element manually
				const countryOption = new Option('Loading...', ctry.country_id, true, true);
				$country.append(countryOption).trigger('change');

				// Fetch actual country name asynchronously
				$.ajax({
					url: '{{ route('countries') }}',
					dataType: 'json'
				}).then(data => {
					const found = data.find(d => String(d.id) === String(ctry.country_id));
					if (found) {
						const option = new Option(found.text, found.id, true, true);
						$country.empty().append(option).trigger('change');
					}
				});
			}

			// --- Restore State ---
			if (ctry.state_id && ctry.country_id) {
				$.ajax({
					url: `{{ url('api/states') }}/${ctry.country_id}`,
					dataType: 'json'
				}).then(data => {
					const found = data.find(d => String(d.id) === String(ctry.state_id));
					if (found) {
						const option = new Option(found.text, found.id, true, true);
						$state.append(option).trigger('change');
					}
				});
			}
		});
	}


});

///////////////////////////////////////////////////////////////////////////////////////////
const ctx = document.getElementById('myChart');
new Chart(ctx, {
	type: 'bar',
	data: {
		labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
		datasets: [{
			label: '# of Votes',
			data: [12, 19, 3, 5, 2, 3],
			borderWidth: 1
		}]
	},
	options: {
		scales: {
			y: {
				beginAtZero: true
			}
		}
	}
});

///////////////////////////////////////////////////////////////////////////////////////////
let calendarEl = document.getElementById('calendar');
let calendar = new Calendar(calendarEl, {
	plugins: [
	multiMonthPlugin,
	dayGridPlugin,
	timeGridPlugin,
	listPlugin,
	momentPlugin,
	bootstrap5Plugin,
	],
	aspectRatio: 1.3,
	height: 2000,
	weekNumbers: true,
	titleFormat: 'D MMMM, YYYY',  // momentPlugin
	themeSystem: 'bootstrap5',   // bootstrap5Plugin
	initialView: 'multiMonthYear',
	headerToolbar: {
		left: 'prev,next today',
		center: 'title',
		right: 'multiMonthYear,dayGridMonth,timeGridWeek'
	},

	// events: {
		// 	url: '{{ route('dashboard') }}',
		// 	method: 'GET',
		// 	extraParams: {
			// 		_token: '{!! csrf_token() !!}',
			// 	},
			// },

			events: [
			{
				title: 'Event 1',
				start: '{{ now() }}', // Date of the event
				description: 'Description of Event 1'
			},
			{
				title: 'Event 2',
				start: '{{ now()->subdays(2) }}', // Date and time
				end: '{{ now()->subday() }}', // Optional end time
				description: 'Description of Event 2'
			},
			{
				title: 'Event 3',
				start: '{{ now()->subdays(6) }}',
				description: 'Description of Event 3'
			}
			],
			eventClick: function(info) {
				// alert(info.event.title + "\n" + info.event.extendedProps.description);
				swal.fire({
					title: info.event.title,
					text: info.event.extendedProps.description,
					icon: 'info',
				});
			},
			eventDidMount: function(info) {
				$(info.el).tooltip({
					title: info.event.extendedProps.description,
					placement: 'top',
					trigger: 'hover',
					container: 'body'
				});
			},
			eventTimeFormat: { // like '14:30:00'
			hour: '2-digit',
			minute: '2-digit',
			second: '2-digit',
			hour12: true
		},

	});
	calendar.render();

	///////////////////////////////////////////////////////////////////////////////////////////
	@endsection
