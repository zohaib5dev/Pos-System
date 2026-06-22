  <div class="flex-1 p-4 sm:p-6 overflow-y-auto" id="productsSection">
      <div class="mb-6 flex items-center gap-3">
          <div class="flex-shrink-0 flex items-center">
              <button x-show="getOfflineStats().totalOffline > 0"
                  @click="showOfflineOrders = true"
                  class="relative me-2 p-2 rounded-lg bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-800 transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
                      x-text="getOfflineStats().pendingSync"></span>
              </button>

              <button
                  @click="
                        darkMode = !darkMode;
                        document.documentElement.classList.toggle('dark', darkMode);
                        localStorage.setItem('darkMode', String(darkMode));
                        if (online && window.Livewire) {
                            try { $wire.toggleDarkMode() } catch(e) {}
                        }
                    "
                  class="relative me-2 w-12 h-12 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 hover:from-gray-200 hover:to-gray-300 dark:hover:from-gray-600 dark:hover:to-gray-700 transition-all duration-300 transform hover:scale-110 active:scale-95 shadow-md hover:shadow-lg flex items-center justify-center group"
                  :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'">

                  <div x-show="!darkMode"
                      x-transition:enter="transition-transform duration-300 ease-out"
                      x-transition:enter-start="rotate-90 scale-0"
                      x-transition:enter-end="rotate-0 scale-100"
                      x-transition:leave="transition-transform duration-200 ease-in"
                      x-transition:leave-start="rotate-0 scale-100"
                      x-transition:leave-end="rotate-90 scale-0"
                      class="absolute inset-0 flex items-center justify-center text-yellow-500">
                      <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" />
                      </svg>
                  </div>

                  <div x-show="darkMode"
                      x-transition:enter="transition-transform duration-300 ease-out"
                      x-transition:enter-start="rotate-90 scale-0"
                      x-transition:enter-end="rotate-0 scale-100"
                      x-transition:leave="transition-transform duration-200 ease-in"
                      x-transition:leave-start="rotate-0 scale-100"
                      x-transition:leave-end="rotate-90 scale-0"
                      class="absolute inset-0 flex items-center justify-center text-indigo-400">
                      <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                      </svg>
                  </div>

                  <span class="absolute inset-0 rounded-xl bg-yellow-400 dark:bg-indigo-400 opacity-0 group-hover:opacity-20 transition-opacity duration-300 blur-md"></span>
              </button>

              @if(getLogo())
              <img src="{{ getLogo() }}" style="width:100px;height:40px;" alt="">
              @else
              <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white whitespace-nowrap">
                  {{ $settings->business_name ?? 'My POS' }}
              </h1>
              @endif

          </div>

          <div class="flex-1 min-w-0 relative flex items-center">
              <div class="flex-1 min-w-0 relative ">
                  <input type="text"
                      :value="productSearch"
                      @input.debounce.300ms="handleProductSearch($event.target.value)"
                      placeholder="Search products by name or SKU..."
                      class="w-full px-6 py-3 pl-12 rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-200 focus:ring-opacity-50 transition-all text-base">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                      </svg>
                  </div>
                  <button x-show="productSearch.length > 0" @click="clearSearch()"
                      class="absolute inset-y-0 right-3 flex items-center px-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 bg-transparent hover:bg-gray-100 dark:hover:bg-gray-600 rounded-r-xl transition-colors">
                      <span class="text-sm mr-1">Clear</span>
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                  </button>
              </div>

              <form action="{{route('logout')}}" method="post" class="flex-shrink-0 ml-2">
                  @csrf
                  <button type="submit"
                      class="flex items-center px-3 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/30 border border-gray-200 dark:border-gray-700 hover:border-red-200 dark:hover:border-red-800 transition-all duration-200 group">
                      <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm mr-2">
                          {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                      </div>
                      <div class="flex-1 text-left mr-3">
                          <div class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-red-600 dark:group-hover:text-red-400">
                              {{ auth()->user()->name ?? 'User' }}
                          </div>
                          <div class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-red-500 dark:group-hover:text-red-400">
                              Log Out
                          </div>
                      </div>
                      <svg class="w-5 h-5 text-gray-400 group-hover:text-red-500 dark:text-gray-500 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                      </svg>
                  </button>
              </form>


              <div x-show="searchResults.length > 0 && productSearch.length >= 2"
                  class="absolute z-10 mt-2 left-0 right-0 mx-auto max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 max-h-96 overflow-y-auto">
                  <template x-for="product in searchResults" :key="product.id">
                      <div @click="addToCart(product)"
                          class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b dark:border-gray-700 last:border-b-0 transition-colors">
                          <div class="flex justify-between items-center">
                              <div>
                                  <div class="font-medium text-gray-900 dark:text-white text-lg" x-text="product.name"></div>
                                  <div class="text-sm text-gray-600 dark:text-gray-400">
                                      SKU: <span x-text="product.sku"></span> | Stock: <span x-text="product.stock_quantity"></span>
                                  </div>
                              </div>
                              <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400" x-text="formatMoney(product.selling_price)"></div>
                          </div>
                      </div>
                  </template>
              </div>
          </div>
      </div>


      <div class="mb-6">
          <div class="flex space-x-2 overflow-x-auto pb-2 scrollbar-thin">
              <div class="flex space-x-2">
                  <button @click="selectedCategoryId = null; filterProducts()"
                      :class="selectedCategoryId === null ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300'"
                      class="px-4 py-2 rounded-full shadow-sm whitespace-nowrap transition-colors">
                      All
                  </button>
                  <template x-for="cat in cachedCategories" :key="cat.id">
                      <button @click="selectedCategoryId = cat.id; filterProducts()"
                          :class="selectedCategoryId === cat.id ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300'"
                          class="px-4 py-2 rounded-full shadow-sm whitespace-nowrap transition-colors"
                          x-text="cat.name">
                      </button>
                  </template>
              </div>
          </div>
      </div>


      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
          <template x-if="displayProducts.length === 0">
              <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                  <p>No products found</p>
              </div>
          </template>
          <template x-for="product in displayProducts" :key="product.id">
              <div @click="addToCart(product)"
                  class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md cursor-pointer p-3 sm:p-4 border border-gray-200 dark:border-gray-700 transition-all hover:scale-105">
                  <div class="w-full h-24 sm:h-32 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-2">
                      <template x-if="product.image">
                          <img :src="'{{ asset('storage/') }}' + '/' + product.image" class="w-full h-full object-cover rounded-lg">
                      </template>
                      <template x-if="!product.image">
                          <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                          </svg>
                      </template>
                  </div>
                  <h3 class="font-medium text-gray-900 dark:text-white text-sm sm:text-base truncate" x-text="product.name"></h3>
                  <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Stock: <span x-text="product.stock_quantity"></span></p>
                  <p class="text-base sm:text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-1" x-text="formatMoney(product.selling_price)"></p>
              </div>
          </template>
      </div>
  </div>