import Vue from 'vue'
import VueColor from 'vue-color'

var Sketch = VueColor.Sketch;
Vue.component('colorpicker', {
	components: {
		'sketch-picker': Sketch,
	},
	template: `
    <div ref="colorpicker">
        <div class="flex w-full">
            <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm cursor-pointer" @click="togglePicker()"><i class="fa fa-tint"></i></span>
            <input type="text" class="w-auto border-gray-300 text-sm" style="height: 42px;" v-model="colorValue" @focus="showPicker()" @input="updateFromInput" readonly/>
            <span class="inline-flex items-center px-3 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm cursor-pointer w-12" @click="togglePicker()" :style="'background-color: ' + colorValue"></span>
        </div>
		<div class="absolute z-50">
        	<sketch-picker :value="colors" @input="updateFromPicker" v-if="displayPicker" />
		</div>
    </div>
		`,
	props: ['color'],
	data() {
		return {
			colors: {
				hex: 'rgba(0, 0, 0, 0)',
			},
			colorValue: '',
			displayPicker: false,
		}
	},
	mounted() {
		if(this.color !== undefined) this.setColor(this.color);
	},
	methods: {
		setColor(color) {
			this.updateColors(color);
			this.colorValue = color;
		},
		updateColors(color) {
			if(color.slice(0, 1) == '#') {
				this.colors = {
					hex: color
				};
			}
			else if(color.slice(0, 4) == 'rgba') {
				var rgba = color.replace(/^rgba?\(|\s+|\)$/g,'').split(','),
					hex = '#' + ((1 << 24) + (parseInt(rgba[0]) << 16) + (parseInt(rgba[1]) << 8) + parseInt(rgba[2])).toString(16).slice(1);
				this.colors = {
					hex: hex,
					a: rgba[3],
				}
			} else {
				this.color = color;
			}
		},
		showPicker() {
			document.addEventListener('click', this.documentClick);
			this.displayPicker = true;
		},
		hidePicker() {
			document.removeEventListener('click', this.documentClick);
			this.displayPicker = false;
		},
		togglePicker() {
			this.displayPicker ? this.hidePicker() : this.showPicker();
		},
		updateFromInput() {
			this.updateColors(this.colorValue);
		},
		updateFromPicker(color) {
			this.colors = color;
			if(color.rgba.a == 1) {
				this.colorValue = color.hex;
			}
			else {
				this.colorValue = 'rgba(' + color.rgba.r + ', ' + color.rgba.g + ', ' + color.rgba.b + ', ' + color.rgba.a + ')';
			}
		},
		documentClick(e) {
			var el = this.$refs.colorpicker,
				target = e.target;
			if(el !== target && !el.contains(target)) {
				this.hidePicker()
				this.$emit('change');
			}
		}
	},
	watch: {
		colorValue(val) {
			if(val) {
				this.updateColors(val);
				this.$emit('input', val);
			}
		},
		'color': function(val) {
			this.colorValue = val;
		}
	},
});
