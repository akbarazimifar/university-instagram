<template>
    <div class="rtl">
        <mu-container class="upload">
            <mu-row>
                <p>عکس مورد نظر خود را انتخاب کنید.</p>
            </mu-row>
            <mu-row>
                <label for="fileUpload">انتخاب عکس</label>
                <input type="file" id="fileUpload" @change="onFileChanged">
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
            };
        },
        methods: {
            onFileChanged(event) {
                this.selectedFile = event.target.files[0];
            },
            onUpload() {
                this.fileError = "";
                let _this = this;
                if (this.selectedFile !== null) {
                    this.isUploading = true;
                    const formData = new FormData();
                    formData.append("file", this.selectedFile, this.selectedFile.name);
                    formData.append("caption", this.caption);
                    this.uploadigProgress = 0;
                    setTimeout(function () {
                        Vue.axios.post("/api/upload", formData, {
                            onUploadProgress: progressEvent => {
                                _this.uploadigProgress = (progressEvent.loaded / progressEvent.total) *
                                    100;
                            }
                        }).then(resp => {
                            _this.isUploading = false;
                        }).catch(error => {
                            _this.fileError = "مشکلی درثبت عکس به وجود آمده است."
                            _this.isUploading = false;
                        })
                    }, 1000)
                } else {
                    this.fileError = "عکسی انتخاب نشده است.";
                }
            }
        }
    };

</script>
