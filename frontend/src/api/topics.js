import client from "./client";

export function listTopics(params = {}) {
  return client.get("/topics", { params }).then((res) => res.data);
}

export function getTopic(id) {
  return client.get(`/topics/${id}`).then((res) => res.data);
}

export function listTopicPosts(id) {
  return client.get(`/topics/${id}/posts`).then((res) => res.data);
}

export function searchTopics(query) {
  return client
    .get("/search/topics", { params: { query } })
    .then((res) => res.data);
}

export function topicStatistics() {
  return client.get("/statistics/topics").then((res) => res.data);
}

export function discussionStarter() {
  return client.get("/discussion-starter").then((res) => res.data);
}

export function createTopic(data) {
  return client.post("/topics", data).then((res) => res.data);
}

export function updateTopic(id, data) {
  return client.put(`/topics/${id}`, data).then((res) => res.data);
}

export function deleteTopic(id) {
  return client.delete(`/topics/${id}`).then((res) => res.data);
}

export function voteTopic(id, value) {
  return client.post(`/topics/${id}/vote`, { value }).then((res) => res.data);
}
