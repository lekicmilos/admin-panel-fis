/** @type {import('tailwindcss').Config} */
export default {
  content: [
		"./resources/**/*.blade.php",
		 "./resources/**/*.js",
		 "./resources/**/*.vue",
		 './vendor/rappasoft/laravel-livewire-tables/resources/views/**/*.blade.php',
		 "./vendor/robsontenorio/mary/src/View/Components/**/*.php",
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
	],
  theme: {
    extend: {},
  },
  plugins: [
		require("daisyui")
	],
  darkMode: 'class',
}


