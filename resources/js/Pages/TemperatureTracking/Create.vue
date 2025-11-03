<template>
  <DefaultAuthenticatedLayout>
    <Head :title="pageTitle" />
    <div class="max-w-7xl mx-auto py-4 sm:py-8 print:p-0 px-2 sm:px-4">
      <!-- Breadcrumb -->
      <nav class="text-xs text-gray-500 mb-4 print:hidden" aria-label="Breadcrumb">
        <ol class="flex flex-wrap gap-1 items-center">
          <li><Link href="/qms/dashboard" class="hover:text-gray-700">Dashboard</Link></li>
          <li>/</li>
          <li><Link :href="route('temperature-tracking.index')" class="hover:text-gray-700">Temperature Tracking</Link></li>
          <li v-if="existing" class="text-gray-700">Edit</li>
          <li v-else class="text-gray-700">New</li>
        </ol>
      </nav>

      <!-- Header -->
      <div class="flex flex-col gap-3 sm:gap-4 mb-4 sm:mb-6">
        <div>
          <h1 class="text-lg sm:text-2xl font-bold tracking-tight flex items-center gap-2">
            <span class="text-xl sm:text-2xl">🔥</span> 
            <span class="break-words">TEMPERATURE TRACKING FORM</span>
          </h1>
          <p class="text-gray-600 text-xs sm:text-sm mt-1 max-w-2xl">Record and monitor temperatures for cooked, holding, vegetable, equipment and receiving logs to ensure food safety compliance.</p>
        </div>
        <div class="flex flex-wrap gap-2 print:hidden">
          <!-- Test Data Button Hidden as per request -->
          <!-- <button @click="insertTestData" type="button" class="flex-1 sm:flex-none px-3 sm:px-4 py-2 rounded text-xs sm:text-sm font-medium bg-green-600 hover:bg-green-700 text-white">Test Data</button> -->
          <button @click="saveDraft" :disabled="form.processing" class="hidden flex-1 sm:flex-none px-3 sm:px-4 py-2 rounded text-xs sm:text-sm font-medium bg-gray-600 hover:bg-gray-700 text-white disabled:opacity-50">Save Draft</button>
          <button @click="submitForm" :disabled="form.processing || correctiveRequired && !hasCorrectiveAction || (!isAdmin && !locationVerified)" class="flex-1 sm:flex-none px-3 sm:px-4 py-2 rounded text-xs sm:text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white disabled:opacity-50">Submit</button>
          <button type="button" @click="printForm" class="hidden flex-1 sm:flex-none px-3 sm:px-4 py-2 rounded text-xs sm:text-sm font-medium bg-indigo-500 hover:bg-indigo-600 text-white">Print</button>
        </div>
      </div>

      <!-- Compliance / Legend Bar -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8 print:hidden">
        <div class="bg-white rounded shadow p-3 sm:p-4 border border-gray-100">
          <h3 class="text-xs font-semibold text-gray-500 tracking-wide mb-1">COMPLIANCE</h3>
          <div class="flex items-end gap-2 sm:gap-3">
            <span class="text-2xl sm:text-3xl font-bold" :class="complianceColor">{{ compliancePercent.toFixed(0) }}%</span>
            <span class="text-[10px] sm:text-xs text-gray-500">within acceptable range</span>
          </div>
        </div>
        <div class="bg-white rounded shadow p-3 sm:p-4 border border-gray-100">
          <h3 class="text-xs font-semibold text-gray-500 tracking-wide mb-1">LEGEND</h3>
          <div class="flex flex-wrap gap-2 sm:gap-4 text-[10px] sm:text-[11px]">
            <div class="flex items-center gap-1"><span class="inline-block w-3 h-3 sm:w-4 sm:h-4 bg-red-200 border border-red-300"></span><span>Out of Range</span></div>
            <div class="flex items-center gap-1"><span class="inline-block w-3 h-3 sm:w-4 sm:h-4 bg-white border"></span><span>OK</span></div>
          </div>
        </div>
        <div class="bg-white rounded shadow p-3 sm:p-4 border border-gray-100 sm:col-span-2 md:col-span-1" v-if="correctiveRequired">
          <h3 class="text-xs font-semibold text-gray-500 tracking-wide mb-1">ACTION REQUIRED</h3>
          <p class="text-[10px] sm:text-xs" v-if="!hasCorrectiveAction">Out-of-range readings detected. Please fill corrective action before submitting.</p>
          <p class="text-[10px] sm:text-xs text-green-600" v-else>Corrective action provided.</p>
        </div>
      </div>

      <!-- GENERAL INFO Card -->
      <div class="bg-white rounded shadow mb-6 sm:mb-8 border border-gray-100">
        <div class="px-3 sm:px-5 py-3 border-b flex flex-col sm:flex-row sm:items-center gap-2">
          <h2 class="font-semibold tracking-wide text-sm">GENERAL INFORMATION</h2>
          <span class="sm:ml-auto text-[11px] text-gray-400">All fields required</span>
        </div>
        <div class="p-3 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 text-sm">
          <div class="space-y-4">
            <div>
              <label class="block text-[11px] font-semibold text-gray-600 mb-1">Store</label>
              <select v-model="form.store_id" :disabled="stores.length === 1" class="w-full border rounded px-2 py-1.5 sm:py-1 text-sm focus:ring-0 focus:outline-none disabled:bg-gray-100 disabled:cursor-not-allowed">
                <option value="">Select store...</option>
                <option v-for="s in stores" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
              <p v-if="stores.length === 1" class="text-[10px] text-green-600 mt-1">✓ Your assigned restaurant</p>
            </div>
            
            <!-- Location Verification -->
            <div v-if="form.store_id && !locationVerified" class="bg-blue-50 border border-blue-200 rounded p-3">
              <h4 class="text-[11px] font-semibold text-blue-800 mb-2">📍 LOCATION VERIFICATION</h4>
              <p class="text-sm text-gray-700 mb-3">You must verify your location at the restaurant before you can submit this form. This ensures you are physically present at the location.</p>
              <button 
                type="button"
                @click="verifyLocation"
                :disabled="locationVerified || verifyingLocation"
                class="w-full px-3 py-2 text-xs font-medium rounded transition-colors"
                :class="locationVerified 
                  ? 'bg-green-600 text-white cursor-default' 
                  : 'bg-blue-600 hover:bg-blue-700 text-white'"
              >
                <span v-if="verifyingLocation">🔄 Verifying your location...</span>
                <span v-else-if="locationVerified" class="break-words">✓ Location Verified at {{ selectedStoreName }}</span>
                <span v-else class="break-words">📍 Verify My Location</span>
              </button>
              <div v-if="locationError" class="mt-2 text-xs" 
                   :class="locationVerified ? 'text-green-600' : 'text-red-600'">
                {{ locationError }}
              </div>
              <p class="text-[10px] text-gray-600 mt-2">You must be within 200 meters of the restaurant to submit</p>
            </div>
            
            <!-- Location Verification Success -->
            <div v-if="form.store_id && locationVerified" class="bg-green-50 border border-green-200 rounded p-3">
              <h4 class="text-[11px] font-semibold text-green-800 mb-2">✅ LOCATION VERIFIED</h4>
              <p class="text-sm text-green-700 mb-2">{{ locationError }}</p>
              <p class="text-[10px] text-green-600">You can now fill out and submit the form.</p>
            </div>
            
            <div>
              <label class="block text-[11px] font-semibold text-gray-600 mb-1">MIC (Manager In Charge)</label>
              <input v-model="form.mic_morning" placeholder="Enter MIC name" class="w-full border rounded px-2 py-1.5 sm:py-1 text-sm" />
              <p class="text-[10px] text-gray-500 mt-1">Enter the name of the Manager In Charge</p>
            </div>
            <div>
              <label class="block text-[11px] font-semibold text-gray-600 mb-1">Shift</label>
              <select v-model="form.shift" class="w-full border rounded px-2 py-1.5 sm:py-1 text-sm focus:ring-0 focus:outline-none">
                <option value="">Select shift...</option>
                <option value="morning">Morning</option>
                <option value="evening">Evening</option>
              </select>
            </div>
          </div>
          <div class="space-y-4">
            <div>
              <label class="block text-[11px] font-semibold text-gray-600 mb-1">Date</label>
              <input type="date" v-model="form.date" class="w-full border rounded px-2 py-1 text-sm" />
            </div>
            <div>
              <label class="block text-[11px] font-semibold text-gray-600 mb-1">Time Slots</label>
              <div class="space-y-2">
                <label v-for="slot in allTimeSlots" :key="slot" class="flex items-center gap-2 text-xs">
                  <input type="checkbox" :value="slot" v-model="selectedTimeSlots" class="rounded" />
                  <span>{{ slot }}</span>
                </label>
              </div>
              <p class="text-[10px] text-gray-500 mt-1">Select the time slots you want to record</p>
            </div>
            <div class="text-[11px] text-gray-600 leading-relaxed">
              <p>Chilled product 36–40°F. Cooked product 160–165°F. Record actual readings per slot.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Cooked Product Temps Card -->
      <section class="bg-white rounded shadow mb-6 sm:mb-8 border border-gray-100">
        <header class="px-3 sm:px-5 py-3 border-b">
          <h2 class="font-semibold tracking-wide text-sm">COOKED PRODUCT TEMPERATURE (160–165°F)</h2>
        </header>
        <div class="p-3 sm:p-5 space-y-4">
          <div class="space-y-2">
            <h3 class="font-semibold text-sm tracking-wide">COOKED PRODUCT</h3>
            <div class="overflow-x-auto">
            <table class="w-full text-xs border">
              <thead>
                <tr>
                  <th class="border px-2 py-1 w-40"></th>
                  <th v-for="prod in primaryProducts" :key="prod" class="border px-2 py-1 text-center">{{ prod }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="slot in timeSlots" :key="slot">
                  <td class="border px-2 py-1 font-medium">{{ slot }}</td>
                  <td v-for="prod in primaryProducts" :key="prod" class="border p-0">
                    <input
                      class="w-full text-center text-[11px] focus:outline-none py-1"
                      :class="getCookedTempClass(form.cooked_products?.[prod]?.[slot])"
                      v-model="form.cooked_products[prod][slot]"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
            </div>
          </div>
        </div>
      </section>

      <!-- Holding Temps Card -->
      <section class="bg-white rounded shadow mb-6 sm:mb-8 border border-gray-100">
        <header class="px-3 sm:px-5 py-3 border-b">
          <h2 class="font-semibold tracking-wide text-sm">HOLDING TEMPERATURE (>=140°F)</h2>
        </header>
        <div class="p-3 sm:p-5 space-y-4">
          <div class="space-y-2">
            <h3 class="font-semibold text-sm tracking-wide">HOLDING TEMPERATURE</h3>
            <div class="overflow-x-auto">
            <table class="w-full text-xs border">
              <thead>
                <tr>
                  <th class="border px-2 py-1 w-40"></th>
                  <th v-for="prod in primaryProducts" :key="prod" class="border px-2 py-1 text-center">{{ prod }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="slot in timeSlots" :key="slot">
                  <td class="border px-2 py-1 font-medium">{{ slot }}</td>
                  <td v-for="prod in primaryProducts" :key="prod" class="border p-0">
                    <input
                      class="w-full text-center text-[11px] focus:outline-none py-1"
                      :class="getHoldingTempClass(form.holding_products?.[prod]?.[slot])"
                      v-model="form.holding_products[prod][slot]"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
            </div>
            <p class="text-[10px] mt-1 font-medium text-red-600">Below 140°F: Discard</p>
          </div>
        </div>
      </section>

      <!-- Vegetables Card -->
      <section class="bg-white rounded shadow mb-6 sm:mb-8 border border-gray-100">
        <header class="px-3 sm:px-5 py-3 border-b">
          <h2 class="font-semibold tracking-wide text-sm">VEGETABLE TEMPERATURE (36–40°F)</h2>
        </header>
        <div class="p-3 sm:p-5 space-y-4">
          <div class="space-y-2">
            <h3 class="font-semibold text-sm tracking-wide">VEGETABLE</h3>
            <div class="overflow-x-auto">
            <table class="w-full text-xs border">
              <thead>
                <tr>
                  <th class="border px-2 py-1 w-40"></th>
                  <th v-for="veg in vegetables" :key="veg" class="border px-2 py-1 text-center">{{ veg }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="slot in timeSlots" :key="slot">
                  <td class="border px-2 py-1 font-medium">{{ slot }}</td>
                  <td v-for="veg in vegetables" :key="veg" class="border p-0">
                    <input
                      class="w-full text-center text-[11px] focus:outline-none py-1"
                      :class="getVegetableTempClass(form.vegetables?.[veg]?.[slot])"
                      v-model="form.vegetables[veg][slot]"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
            </div>
          </div>
        </div>
      </section>

      <!-- Sanitizer Solution Card -->
      <section class="bg-white rounded shadow mb-6 sm:mb-8 border border-gray-100">
        <header class="px-3 sm:px-5 py-3 border-b">
          <h2 class="font-semibold tracking-wide text-sm">SANITIZER SOLUTION (150–300 ppm)</h2>
        </header>
        <div class="p-3 sm:p-5 space-y-4">
          <div class="space-y-2">
            <h3 class="font-semibold text-sm tracking-wide">SANITIZER SOLUTION (ppm)</h3>
            <div class="overflow-x-auto">
            <table class="w-full text-xs border">
              <tbody>
                <tr v-for="slot in timeSlots" :key="slot">
                  <td class="border px-2 py-1 w-32">{{ slot }}</td>
                  <td class="border px-2 py-1">
                    <input
                      v-model="form.sanitizer[slot]"
                      :class="sanitizerCellClass(form.sanitizer[slot])"
                      class="w-full text-center focus:outline-none"
                      placeholder="_____ppm"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
            </div>
            <p v-if="sanitizerOutOfRange" class="text-[10px] sm:text-xs text-red-600 mt-1">Some sanitizer readings are out of 150–300 ppm range. Please provide corrective action.</p>
          </div>
        </div>
      </section>

      <!-- Corrective Action -->
      <section class="bg-white rounded shadow mb-6 sm:mb-8 border" :class="correctiveRequired && !hasCorrectiveAction ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-100'">
        <header class="px-3 sm:px-5 py-3 border-b" :class="correctiveRequired && !hasCorrectiveAction ? 'bg-red-50' : ''">
          <div class="flex items-center justify-between">
            <h2 class="font-semibold tracking-wide text-sm" :class="correctiveRequired && !hasCorrectiveAction ? 'text-red-700' : ''">
              CORRECTIVE ACTION
              <span v-if="correctiveRequired && !hasCorrectiveAction" class="text-red-600 ml-2">*REQUIRED*</span>
            </h2>
            <span v-if="correctiveRequired && hasCorrectiveAction" class="text-green-600 text-xs font-semibold flex items-center gap-1">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              Completed
            </span>
          </div>
          <p v-if="correctiveRequired && !hasCorrectiveAction" class="text-[10px] sm:text-xs text-red-600 mt-1">⚠️ Out-of-range temperatures detected. Please describe the corrective actions taken.</p>
        </header>
        <div class="p-3 sm:p-5 space-y-4">
          <textarea 
            v-model="form.corrective_action_upper" 
            rows="4" 
            class="w-full border rounded text-sm p-2" 
            :class="correctiveRequired && !hasCorrectiveAction ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''"
            placeholder="Describe any corrective actions taken for out-of-range readings..."
            :required="correctiveRequired"
          ></textarea>
        </div>
      </section>

      <!-- Equipment Temperature -->
      <section class="bg-white rounded shadow mb-6 sm:mb-8 border border-gray-100">
        <header class="px-3 sm:px-5 py-3 border-b">
          <h2 class="font-semibold tracking-wide text-sm">EQUIPMENT TEMPERATURE</h2>
        </header>
        <div class="p-3 sm:p-5 space-y-4">
          <div class="overflow-x-auto">
          <table class="w-full text-xs border">
            <thead>
              <tr>
                <th class="border px-2 py-1">Equipment</th>
                <th class="border px-2 py-1">Temperature Range</th>
                <th class="border px-2 py-1">Status (Y/N)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="border px-2 py-1 font-medium">Reach-in Chiller (Thaw)</td>
                <td class="border px-2 py-1">36–40°F</td>
                <td class="border px-1 py-1">
                  <select v-model="form.equipment.reach_in_thaw.chill" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="border px-2 py-1 font-medium">Reach-in Chiller (Veg)</td>
                <td class="border px-2 py-1">36–40°F</td>
                <td class="border px-1 py-1">
                  <select v-model="form.equipment.reach_in_veg.chill" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="border px-2 py-1 font-medium">Walk-in Freezer (Frozen)</td>
                <td class="border px-2 py-1">0 ±10°F</td>
                <td class="border px-1 py-1">
                  <select v-model="form.equipment.walk_in_frozen.freeze" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="border px-2 py-1 font-medium">Walk-in Freezer (Reach-in)</td>
                <td class="border px-2 py-1">0 ±10°F</td>
                <td class="border px-1 py-1">
                  <select v-model="form.equipment.walk_in_reach.freeze" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
          </div>
        </div>
      </section>

      <!-- Product Receiving -->
      <section class="bg-white rounded shadow mb-6 sm:mb-8 border border-gray-100">
        <header class="px-3 sm:px-5 py-3 border-b">
          <h2 class="font-semibold tracking-wide text-sm">PRODUCT RECEIVING</h2>
        </header>
        <div class="p-3 sm:p-5 space-y-4">
          <div class="overflow-x-auto">
          <table class="w-full text-xs border">
            <thead>
              <tr>
                <th class="border px-2 py-1">Product</th>
                <th class="border px-2 py-1">Date Received</th>
                <th class="border px-2 py-1">Production Date</th>
                <th class="border px-2 py-1">Expiry Date</th>
                <th class="border px-2 py-1">Frozen/Defrosted (Y/N)</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="prod in primaryProductsExtended" :key="prod">
                <td class="border px-2 py-1 font-medium">{{ prod }}</td>
                <td class="border px-1 py-1"><input type="date" v-model="form.receiving_products[prod].received" class="w-full text-center" /></td>
                <td class="border px-1 py-1"><input type="date" v-model="form.receiving_products[prod].production" class="w-full text-center" /></td>
                <td class="border px-1 py-1"><input type="date" v-model="form.receiving_products[prod].expiry" class="w-full text-center" /></td>
                <td class="border px-1 py-1">
                  <select v-model="form.receiving_products[prod].yn" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
          </div>
          <p class="text-[10px] sm:text-[11px] mt-2 text-gray-600">Frozen status: Y = Frozen, N = Defrosted. If N (Defrosted), Area Manager will be notified.</p>
        </div>
      </section>

      <!-- Vegetable Receiving -->
      <section class="bg-white rounded shadow mb-6 sm:mb-8 border border-gray-100">
        <header class="px-3 sm:px-5 py-3 border-b">
          <h2 class="font-semibold tracking-wide text-sm">VEGETABLE RECEIVING PRODUCTS</h2>
        </header>
        <div class="p-3 sm:p-5 space-y-4">
          <div class="overflow-x-auto">
          <table class="w-full text-xs border">
            <thead>
              <tr>
                <th class="border px-2 py-1">Vegetable</th>
                <th class="border px-2 py-1">Quantity</th>
                <th class="border px-2 py-1">Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="veg in vegetables" :key="veg">
                <td class="border px-2 py-1 font-medium">{{ veg }}</td>
                <td class="border px-1 py-1"><input v-model="form.vegetable_receiving[veg].qty" class="w-full text-center" placeholder="Qty" /></td>
                <td class="border px-1 py-1"><input type="date" v-model="form.vegetable_receiving[veg].date" class="w-full text-center" /></td>
              </tr>
            </tbody>
          </table>
          </div>
        </div>
      </section>

      <!-- Receiving Shift Turn Over -->
      <section class="bg-white rounded shadow mb-6 sm:mb-8 border border-gray-100">
        <header class="px-3 sm:px-5 py-3 border-b">
          <h2 class="font-semibold tracking-wide text-sm">RECEIVING SHIFT TURN OVER</h2>
        </header>
        <div class="p-3 sm:p-5 space-y-4">
          <div class="overflow-x-auto">
          <table class="w-full text-xs border">
            <thead>
              <tr>
                <th class="border px-2 py-1">MIC</th>
                <th class="border px-2 py-1">Frozen</th>
                <th class="border px-2 py-1">Packaging</th>
                <th class="border px-2 py-1">Bread</th>
                <th class="border px-2 py-1">Maintenance</th>
                <th class="border px-2 py-1">Signature</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="border px-2 py-1 font-medium">MIC (Morning)</td>
                <td class="border px-1 py-1">
                  <select v-model="form.shift_turnover.morning.frozen" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="✔">✔</option>
                    <option value="✖">✖</option>
                  </select>
                </td>
                <td class="border px-1 py-1">
                  <select v-model="form.shift_turnover.morning.packaging" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="✔">✔</option>
                    <option value="✖">✖</option>
                  </select>
                </td>
                <td class="border px-1 py-1">
                  <select v-model="form.shift_turnover.morning.bread" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="✔">✔</option>
                    <option value="✖">✖</option>
                  </select>
                </td>
                <td class="border px-1 py-1">
                  <select v-model="form.shift_turnover.morning.maintenance" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="✔">✔</option>
                    <option value="✖">✖</option>
                  </select>
                </td>
                <td class="border px-1 py-1"><input v-model="form.shift_turnover.morning.signature" class="w-full text-center" placeholder="Signature" /></td>
              </tr>
              <tr>
                <td class="border px-2 py-1 font-medium">MIC (Evening)</td>
                <td class="border px-1 py-1">
                  <select v-model="form.shift_turnover.evening.frozen" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="✔">✔</option>
                    <option value="✖">✖</option>
                  </select>
                </td>
                <td class="border px-1 py-1">
                  <select v-model="form.shift_turnover.evening.packaging" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="✔">✔</option>
                    <option value="✖">✖</option>
                  </select>
                </td>
                <td class="border px-1 py-1">
                  <select v-model="form.shift_turnover.evening.bread" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="✔">✔</option>
                    <option value="✖">✖</option>
                  </select>
                </td>
                <td class="border px-1 py-1">
                  <select v-model="form.shift_turnover.evening.maintenance" class="w-full text-center text-xs">
                    <option value="">-</option>
                    <option value="✔">✔</option>
                    <option value="✖">✖</option>
                  </select>
                </td>
                <td class="border px-1 py-1"><input v-model="form.shift_turnover.evening.signature" class="w-full text-center" placeholder="Signature" /></td>
              </tr>
            </tbody>
          </table>
          </div>
          <p class="text-[10px] sm:text-xs mt-2 leading-snug text-gray-600">Mark "✔" if fully received without missing items; "✖" if there are shortages. ✖ marks will notify Area Manager.</p>
        </div>
      </section>

      <!-- Bottom Action Bar (sticky) -->
      <div class="h-16"></div>
      <div class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-sm py-3 px-4 flex gap-2 justify-end print:hidden">
        <!-- Insert Test Data Button Hidden as per request -->
        <!--
        <button v-if="props.isAdmin" @click="insertTestData" type="button" class="px-4 py-2 rounded text-sm font-medium bg-purple-600 hover:bg-purple-700 text-white flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Insert Test Data
        </button>
        -->
        <button @click="saveDraft" :disabled="form.processing" class="hidden px-4 py-2 rounded text-sm font-medium bg-gray-600 hover:bg-gray-700 text-white disabled:opacity-50">Save Draft</button>
        <button @click="submitForm" :disabled="form.processing || correctiveRequired && !hasCorrectiveAction || (!isAdmin && !locationVerified)" class="px-4 py-2 rounded text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white disabled:opacity-50">Submit</button>
        <button @click="printForm" type="button" class="hidden px-4 py-2 rounded text-sm font-medium bg-indigo-500 hover:bg-indigo-600 text-white">Print</button>
      </div>
    </div>
  </DefaultAuthenticatedLayout>
</template>

<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, reactive, computed, watch } from 'vue';
import ReceivingTable from './Partials/ReceivingTable.vue';

const props = defineProps({
  stores: {
    type: Array,
    default: () => []
  },
  isAdmin: {
    type: Boolean,
    default: false
  },
  form: {
    type: Object,
    default: null
  }
});

const page = usePage();

// Time slots & products
const allTimeSlots = ['11AM-2PM','3PM-6PM','7PM-10PM','11PM-2AM'];
const selectedTimeSlots = ref([...allTimeSlots]); // All slots selected by default
const timeSlots = computed(() => selectedTimeSlots.value);

const primaryProducts = ['KIDS CHICKEN BURGER','REGULAR CHICKEN','SULTAN BEEF','DOUBLE G BEEF','CRISPY CHICKEN','GRILLED CHICKEN'];
const sideProducts = ['JULIENNE','MAC & CHEESE','NACHOS'];
const vegetables = ['LETTUCE','TOMATO','PICKLES','ONION'];
const primaryProductsExtended = [...primaryProducts, 'NACHOS'];
const sideProductsExtended = ['JULIENNE','MAC & CHEESE','NACHOS'];
const equipmentList = [
  { key: 'reach_in_thaw', label: 'REACH IN CHILLER (THAW.)' },
  { key: 'reach_in_veg', label: 'REACH IN CHILLER (VEG)' },
  { key: 'walk_in_frozen', label: 'WALK IN FREEZER (FROZEN)' },
  { key: 'walk_in_reach', label: 'WALK IN FREEZER (REACH IN)' },
];
const shiftKeys = ['frozen','packaging','bread','maintenance','signature'];

// Initialize data helpers
function emptyMatrix(products){
  const obj = {}; products.forEach(p=>{obj[p]={}; allTimeSlots.forEach(t=>obj[p][t]='');}); return obj;
}
function emptyReceiving(products){
  const o={}; products.forEach(p=>o[p]={received:'',production:'',expiry:'',yn:''}); return o;
}
function emptyVegetableReceiving(){
  const o={}; vegetables.forEach(v=>o[v]={qty:'',date:''}); return o;
}
function emptyEquipment(){
  const o={}; equipmentList.forEach(e=>o[e.key]={chill:'',freeze:''}); return o;
}

const existing = props.form || null;

// DEBUG: Log form data when editing
if (existing) {
  console.log('=== EDIT PAGE FORM DATA ===');
  console.log('Existing Form:', existing);
  console.log('Store ID:', existing.store_id);
  console.log('Date:', existing.date);
  console.log('MIC Morning:', existing.mic_morning);
  console.log('Cooked Products:', existing.cooked_products);
  console.log('Vegetables:', existing.vegetables);
  console.log('Side Cooked:', existing.side_cooked);
}

const pageTitle = existing ? 'Edit Temperature Tracking' : 'New Temperature Tracking Form';

// Location verification state
const locationVerified = ref(false);
const verifyingLocation = ref(false);
const userCoordinates = ref(null);
const locationError = ref('');

const form = useForm({
  store_id: existing?.store_id || '',
  date: existing?.date || new Date().toISOString().slice(0,10),
  mic_morning: existing?.mic_morning || '',
  mic_evening: existing?.mic_evening || '',
  shift: existing?.shift || '',
  user_latitude: null,
  user_longitude: null,
  cooked_products: existing?.cooked_products || emptyMatrix(primaryProducts),
  holding_products: existing?.holding_products || emptyMatrix(primaryProducts),
  side_cooked: existing?.side_cooked || emptyMatrix(sideProducts),
  vegetables: existing?.vegetables || emptyMatrix(vegetables),
  side_holding: existing?.side_holding || emptyMatrix(sideProducts),
  sanitizer: existing?.sanitizer || {},
  corrective_action: existing?.corrective_action || '',
  corrective_action_upper: existing?.corrective_action_upper || '',
  corrective_action_lower: existing?.corrective_action_lower || '',
  equipment: existing?.equipment || emptyEquipment(),
  vegetable_receiving: existing?.vegetable_receiving || emptyVegetableReceiving(),
  receiving_products: existing?.receiving_products || emptyReceiving(primaryProductsExtended),
  product_receiving_side: existing?.product_receiving_side || emptyReceiving(sideProductsExtended),
  shift_turnover: existing?.shift_turnover || { morning: {frozen:'', packaging:'', bread:'', maintenance:'', signature:''}, evening: {frozen:'', packaging:'', bread:'', maintenance:'', signature:''}},
  status: existing?.status || 'Draft'
});

// Auto-select store if user has only one restaurant
if (!existing && props.stores.length === 1) {
  form.store_id = props.stores[0].id;
}

// Computed property for selected store name
const selectedStoreName = computed(() => {
  const store = props.stores.find(s => s.id === form.store_id);
  return store ? store.name : '';
});

function printForm() {
  window.print();
}

function saveDraft(){
  form.status = 'Draft';
  persist();
}

// Calculate distance between two coordinates (Haversine formula)
const calculateDistance = (lat1, lon1, lat2, lon2) => {
    const R = 6371e3; // Earth's radius in meters
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lon2 - lon1) * Math.PI / 180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c; // Distance in meters
};

// Get selected restaurant with coordinates
const getSelectedStore = computed(() => {
    return props.stores.find(s => s.id === form.store_id);
});

function verifyLocation() {
  if (!form.store_id) {
    alert('Please select a store first');
    return;
  }
  
  const store = getSelectedStore.value;
  
  if (!store) {
    locationError.value = 'Please select a store first';
    return;
  }
  
  if (!store.latitude || !store.longitude) {
    locationError.value = 'Store location not set. Please contact admin.';
    return;
  }
  
  verifyingLocation.value = true;
  locationError.value = '';
  
  if ('geolocation' in navigator) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const userLat = position.coords.latitude;
        const userLon = position.coords.longitude;
        const distance = calculateDistance(
            userLat, 
            userLon, 
            store.latitude, 
            store.longitude
        );
        
        userCoordinates.value = {
          latitude: userLat,
          longitude: userLon,
          accuracy: position.coords.accuracy
        };
        
        // Store coordinates in form data for backend validation
        form.user_latitude = userLat;
        form.user_longitude = userLon;
        
        // Check if within 200 meters of store
        if (distance <= 200) {
            locationVerified.value = true;
            locationError.value = `✅ Location verified! You are ${Math.round(distance)}m from ${store.name}`;
        } else {
            locationVerified.value = false;
            locationError.value = `❌ You are ${Math.round(distance)}m away from ${store.name}. You must be within 200m to submit.`;
        }
        
        verifyingLocation.value = false;
      },
      (error) => {
        verifyingLocation.value = false;
        locationError.value = 'Location access denied. Please enable location services.';
        console.error('Geolocation error:', error);
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
      }
    );
  } else {
    verifyingLocation.value = false;
    locationError.value = 'Geolocation not supported by your browser';
  }
}

// Reset location when store changes
const handleStoreChange = () => {
    locationVerified.value = false;
    locationError.value = '';
    form.user_latitude = null;
    form.user_longitude = null;
};

// Watch for store changes
watch(() => form.store_id, () => {
    handleStoreChange();
});

function submitForm(){
  // Check location verification for non-admin users
  if (!props.isAdmin && !locationVerified.value) {
    alert('Please verify your location before submitting the form');
    return;
  }
  
  form.status = 'Submitted';
  persist();
}
function insertTestData(){
  // General Information
  form.store_id = props.stores.length > 0 ? props.stores[0].id : '';
  form.date = new Date().toISOString().slice(0,10);
  form.mic_morning = 'John Smith';
  form.mic_evening = 'Sarah Johnson';
  form.shift = 'morning';

  // Cooked Product Temperatures (160-165°F target)
  form.cooked_products = {
    'KIDS CHICKEN BURGER': {'11AM-2PM': '162', '3PM-6PM': '164', '7PM-10PM': '163', '11PM-2AM': '161'},
    'REGULAR CHICKEN': {'11AM-2PM': '163', '3PM-6PM': '162', '7PM-10PM': '164', '11PM-2AM': '162'},
    'SULTAN BEEF': {'11AM-2PM': '164', '3PM-6PM': '163', '7PM-10PM': '162', '11PM-2AM': '163'},
    'DOUBLE G BEEF': {'11AM-2PM': '161', '3PM-6PM': '164', '7PM-10PM': '163', '11PM-2AM': '162'},
    'CRISPY CHICKEN': {'11AM-2PM': '162', '3PM-6PM': '161', '7PM-10PM': '164', '11PM-2AM': '163'},
    'GRILLED CHICKEN': {'11AM-2PM': '163', '3PM-6PM': '162', '7PM-10PM': '161', '11PM-2AM': '164'}
  };

  // Holding Temperatures (≥140°F target)
  form.holding_products = {
    'KIDS CHICKEN BURGER': {'11AM-2PM': '142', '3PM-6PM': '145', '7PM-10PM': '144', '11PM-2AM': '143'},
    'REGULAR CHICKEN': {'11AM-2PM': '144', '3PM-6PM': '142', '7PM-10PM': '145', '11PM-2AM': '143'},
    'SULTAN BEEF': {'11AM-2PM': '145', '3PM-6PM': '143', '7PM-10PM': '142', '11PM-2AM': '144'},
    'DOUBLE G BEEF': {'11AM-2PM': '143', '3PM-6PM': '145', '7PM-10PM': '144', '11PM-2AM': '142'},
    'CRISPY CHICKEN': {'11AM-2PM': '142', '3PM-6PM': '144', '7PM-10PM': '145', '11PM-2AM': '143'},
    'GRILLED CHICKEN': {'11AM-2PM': '144', '3PM-6PM': '142', '7PM-10PM': '143', '11PM-2AM': '145'}
  };

  // Vegetable Temperatures (36-40°F target)
  form.vegetables = {
    'LETTUCE': {'11AM-2PM': '38', '3PM-6PM': '37', '7PM-10PM': '39', '11PM-2AM': '38'},
    'TOMATO': {'11AM-2PM': '37', '3PM-6PM': '39', '7PM-10PM': '38', '11PM-2AM': '37'},
    'PICKLES': {'11AM-2PM': '39', '3PM-6PM': '38', '7PM-10PM': '37', '11PM-2AM': '39'},
    'ONION': {'11AM-2PM': '38', '3PM-6PM': '37', '7PM-10PM': '39', '11PM-2AM': '38'}
  };

  // Sanitizer Solution (150-300 ppm target)
  form.sanitizer = {
    '11AM-2PM': '200',
    '3PM-6PM': '250',
    '7PM-10PM': '220',
    '11PM-2AM': '180'
  };

  // Equipment Status
  form.equipment = {
    reach_in_thaw: {chill: 'Y'},
    reach_in_veg: {chill: 'Y'},
    walk_in_frozen: {freeze: 'Y'},
    walk_in_reach: {freeze: 'Y'}
  };

  // Product Receiving
  const today = new Date().toISOString().slice(0,10);
  const tomorrow = new Date(Date.now() + 86400000).toISOString().slice(0,10);
  const nextWeek = new Date(Date.now() + 7*86400000).toISOString().slice(0,10);
  
  form.receiving_products = {
    'KIDS CHICKEN BURGER': {received: today, production: today, expiry: nextWeek, yn: 'Y'},
    'REGULAR CHICKEN': {received: today, production: today, expiry: nextWeek, yn: 'Y'},
    'SULTAN BEEF': {received: today, production: today, expiry: nextWeek, yn: 'Y'},
    'DOUBLE G BEEF': {received: today, production: today, expiry: nextWeek, yn: 'N'},
    'CRISPY CHICKEN': {received: today, production: today, expiry: nextWeek, yn: 'Y'},
    'GRILLED CHICKEN': {received: today, production: today, expiry: nextWeek, yn: 'Y'},
    'NACHOS': {received: today, production: today, expiry: nextWeek, yn: 'Y'}
  };

  // Vegetable Receiving
  form.vegetable_receiving = {
    'LETTUCE': {qty: '50 lbs', date: today},
    'TOMATO': {qty: '30 lbs', date: today},
    'PICKLES': {qty: '20 lbs', date: today},
    'ONION': {qty: '25 lbs', date: today}
  };

  // Shift Turnover
  form.shift_turnover = {
    morning: {
      frozen: '✔',
      packaging: '✔',
      bread: '✔',
      maintenance: '✖',
      signature: 'J. Smith'
    },
    evening: {
      frozen: '✔',
      packaging: '✔',
      bread: '✔',
      maintenance: '✔',
      signature: 'S. Johnson'
    }
  };

  // Corrective Action
  form.corrective_action_upper = 'Maintenance issue with freezer unit reported - technician scheduled for tomorrow morning. Double G Beef was defrosted due to temperature fluctuation.';
}
function persist(){
  if(existing){
    form.put(route('temperature-tracking.update', existing.id), {
      onSuccess: () => {
        console.log('Form updated successfully');
      },
      onError: (errors) => {
        console.error('Form update errors:', errors);
      }
    });
  } else {
    form.post(route('temperature-tracking.store'), {
      onSuccess: () => {
        console.log('Form submitted successfully');
      },
      onError: (errors) => {
        console.error('Form submission errors:', errors);
      }
    });
  }
}
// Derived compliance metrics (simple count of in-range entries across key matrices)
const matrices = ['cooked_products','holding_products','side_cooked','side_holding','vegetables'];
function flatEntries(){
  const arr=[]; matrices.forEach(m=>{ const block=form[m]; if(!block) return; Object.values(block).forEach(prod=>{ Object.values(prod).forEach(v=>{ if(v!=='' && v!=null) arr.push({m,value:parseFloat(v)}); }); }); }); return arr; }
function isInRange(m,value){
  if(isNaN(value)) return true; // ignore invalid
  if(m==='vegetables') return value>=36 && value<=40;
  if(m==='cooked_products'|| m==='side_cooked') return value>=160 && value<=165;
  if(m==='holding_products'|| m==='side_holding') return value>=140; // upper not enforced
  return true;
}
const compliancePercent = computed(()=>{
  const entries = flatEntries(); if(entries.length===0) return 100;
  const good = entries.filter(e=>isInRange(e.m,e.value)).length;
  return (good/entries.length)*100;
});
const complianceColor = computed(()=> compliancePercent.value>=95 ? 'text-green-600':'text-yellow-600');
// Sanitizer PPM validation
function sanitizerCellClass(val) {
  if (!val) return '';
  const num = parseFloat(val);
  if (isNaN(num)) return '';
  if (num < 150 || num > 300) return 'bg-red-200';
  return '';
}

// Temperature validation functions
function getCookedTempClass(val) {
  if (!val) return '';
  const num = parseFloat(val);
  if (isNaN(num)) return '';
  if (num < 160 || num > 165) return 'bg-red-200';
  return '';
}

function getHoldingTempClass(val) {
  if (!val) return '';
  const num = parseFloat(val);
  if (isNaN(num)) return '';
  if (num < 140) return 'bg-red-200';
  return '';
}

function getVegetableTempClass(val) {
  if (!val) return '';
  const num = parseFloat(val);
  if (isNaN(num)) return '';
  if (num < 36 || num > 40) return 'bg-red-200';
  return '';
}

const sanitizerOutOfRange = computed(() => {
  return timeSlots.value.some(slot => {
    const v = form.sanitizer[slot];
    if (!v) return false;
    const num = parseFloat(v);
    return !isNaN(num) && (num < 150 || num > 300);
  });
});
const correctiveRequired = computed(()=>{
  const entries = flatEntries();
  const tempOut = entries.some(e=>!isInRange(e.m,e.value));
  return tempOut || sanitizerOutOfRange.value;
});
const hasCorrectiveAction = computed(()=> (form.corrective_action_upper+form.corrective_action_lower).trim().length>0);
</script>

<style scoped>
@media print {
  input, textarea, select { border: none !important; padding: 0 !important; }
  .print\:hidden { display: none !important; }
  body { background: #fff; }
}
</style>
