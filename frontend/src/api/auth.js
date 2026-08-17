import client from "./client";

export function register(data) {
  return client.post("/register", data).then((res) => res.data);
}

export function login(data) {
  return client.post("/login", data).then((res) => res.data);
}

export function logout() {
  return client.post("/logout").then((res) => res.data);
}

export function getCurrentUser() {
  return client.get("/user").then((res) => res.data);
}

export function forgotPassword(email) {
  return client.post("/forgot-password", { email }).then((res) => res.data);
}

export function resetPassword(data) {
  return client.post("/reset-password", data).then((res) => res.data);
}

export function uploadAvatar(file) {
  const formData = new FormData();
  formData.append("avatar", file);
  return client
    .post("/user/avatar", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    })
    .then((res) => res.data);
}
