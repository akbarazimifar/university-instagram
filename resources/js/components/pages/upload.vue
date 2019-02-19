<template>
  <div class="rtl">
    <mu-container class="upload">
      <mu-row>
        <p>عکس مورد نظر خود را انتخاب کنید.</p>
      </mu-row>
      <mu-row>
        <label for="fileUpload">انتخاب عکس</label>
        <input type="file" ref="fileupload" @change="onFileChanged" accept="image/*">
        <div class="mu-form-item__error mu-form" v-if="fileError">{{fileError}}</div>
      </mu-row>
      <mu-row>
        <mu-text-field v-model="caption" placeholder="توضیحات" multi-line :rows="3" :rows-max="6"></mu-text-field>
      </mu-row>
      <mu-row>
        <mu-button color="primary" @click="onUpload">آپلود</mu-button>
      </mu-row>
      <mu-row>
        <mu-row v-if="isUploading" class="progressBar">در حال بارگزاری
          <mu-linear-progress :value="uploadigProgress" mode="determinate" color="green"></mu-linear-progress>
        </mu-row>
      </mu-row>
      <mu-row v-if="uploadSucceed">
        <span class="mu-form">عکس شما با موفقیت آپلود شد.</span>
      </mu-row>
    </mu-container>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedFile: null,
      uploadigProgress: 0,
      isUploading: false,
      caption: "",
      fileError: "",
      uploadSucceed: false
    };
  },
  methods: {
    onFileChanged(event) {
      this.selectedFile = event.target.files[0];
      this.uploadSucceed = false;
    },
    onUpload() {
      this.fileError = "";
      let _this = this;
      if (this.selectedFile !== null) {
        this.isUploading = true;
        this.uploadSucceed = false;
        const formData = new FormData();
        formData.append("file", this.selectedFile, this.selectedFile.name);
        formData.append("caption", this.caption);
        this.uploadigProgress = 0;
        setTimeout(function() {
          Vue.axios
            .post("/api/upload", formData, {
              onUploadProgress: progressEvent => {
                _this.uploadigProgress =
                  (progressEvent.loaded / progressEvent.total) * 100;
              }
            })
            .then(resp => {
              _this.isUploading = false;
              _this.uploadSucceed = true;
              _this.selectedFile = null;
              _this.fileUploadClear();
            })
            .catch(error => {
              _this.fileError = "مشکلی درثبت عکس به وجود آمده است.";
              _this.isUploading = false;
            });
        }, 1000);
      } else {
        this.fileError = "عکسی انتخاب نشده است.";
      }
    },
    fileUploadClear() {
      const input = this.$refs.fileupload;
      input.type = "text";
      input.type = "file";
      this.caption = "";
    }
  }
};
</script>
