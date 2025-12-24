<template>
  <div class="select-country-phone">
    <label for="phone-input" class="phone-input-label">Phone Number:</label>
    <div class="phone-input-wrapper">
      <div class="custom-dropdown">
        <div class="selected" @click="toggleDropdown">
          <img
            v-if="selectedCountry"
            :src="`https://flagcdn.com/w40/${selectedCountry.iso2.toLowerCase()}.png`"
            class="flag"
            alt=""
          />
          <span>
            {{ selectedCountry ? ` (${selectedCountry.dialCode})` : "Select a country" }}
          </span>
          <span class="caret" :class="{ 'caret-open': dropdownOpen }">
            &#9662;
          </span>
        </div>

        <div v-if="dropdownOpen" class="dropdown-menu">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search country or code..."
            class="dropdown-search"
          />
          <ul>
            <li
              v-for="country in filteredCountries"
              :key="country.iso2"
              @click="selectCountry(country)"
              class="dropdown-item"
            >
              <img
                :src="`https://flagcdn.com/w40/${country.iso2.toLowerCase()}.png`"
                class="flag"
                alt=""
              />
              <span>{{ country.name }} ({{ country.dialCode }})</span>
            </li>
          </ul>
        </div>
      </div>

      <input
        id="phone-input"
        type="text"
        v-model="phoneNumber"
        placeholder="Enter phone number"
      />
    </div>
  </div>
</template>

<script>
import { allCountries } from "country-telephone-data";

export default {
  name: "SelectCountryPhone",
  data() {
    return {
      selectedCountry: null,
      selectedPhoneCode: "",
      phoneNumber: "",
      countries: [],
      dropdownOpen: false,
      searchQuery: "",
    };
  },
  computed: {
    filteredCountries() {
      if (!this.searchQuery) {
        return this.countries;
      }
      return this.countries.filter((country) =>
        country.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        country.dialCode.includes(this.searchQuery)
      );
    },
    fullPhoneNumber() {
      return this.selectedPhoneCode + this.phoneNumber;
    },
  },
  methods: {
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    },
    selectCountry(country) {
      this.selectedCountry = country;
      this.selectedPhoneCode = `+${country.dialCode}`;
      this.dropdownOpen = false;
      this.$emit("country-selected", country);
    },
  },
  watch: {
    phoneNumber(newValue) {
      this.$emit("phone-changed", {
        phoneNumber: newValue,
        fullPhoneNumber: this.fullPhoneNumber,
      });
    },
  },
  created() {
    this.countries = allCountries.map((country) => ({
      name: country.name,
      dialCode: country.dialCode,
      iso2: country.iso2,
    }));
    if (this.countries.length > 0) {
      this.selectedCountry = this.countries[0];
      this.selectedPhoneCode = `+${this.countries[0].dialCode}`;
    }
  },
};
</script>


<style scoped>
.select-country-phone {
  font-family: Arial, sans-serif;
  margin-block: 0;
  max-inline-size: 400px;
}

.phone-input-wrapper {
  display: flex;
  align-items: center;
  gap: 8px;
}

.custom-dropdown {
  position: relative;
  flex: 1;
}

.selected {
  display: flex;
  align-items: center;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: #fff;
  cursor: pointer;
  gap: 8px;
}

.dropdown-menu {
  position: absolute;
  z-index: 1000;
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 4px;
  margin: 0;
  background: #fff;
  inset-block-start: 100%;
  inset-block-start: calc(100% + 4px);
  inset-inline: 0;
  list-style: none;
  max-block-size: 200px;
  min-inline-size: 250px;
  overflow-y: auto;
}

.dropdown-item {
  display: flex;
  align-items: center;
  padding: 8px;
  cursor: pointer;
  gap: 8px;
}

.dropdown-item:hover {
  background: #f0f0f0;
}

.flag {
  border-radius: 2px;
  block-size: 15px;
  inline-size: 20px;
}

input {
  flex: 2;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.phone-code-display {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: #f9f9f9;
  margin-block-start: 16px;
}

.caret {
  color: #666;
  font-size: 12px;
  margin-inline-start: auto;
  transition: transform 0.3s ease;
}

.caret-open {
  transform: rotate(180deg);
}

.phone-input-label {
  font-size: 0.8125rem !important;
  font-weight: 400;
  letter-spacing: normal !important;
  line-height: 1.25rem;
  margin-block-end: 4px;
  text-transform: none !important;
}

.dropdown-search {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
  inline-size: 100%;
  margin-block-end: 8px;
}
</style>

