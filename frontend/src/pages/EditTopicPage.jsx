import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import * as topicsApi from "../api/topics";
import * as categoriesApi from "../api/categories";
import { ErrorMessage, LoadingSpinner } from "../components/StatusMessage";

export default function EditTopicPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [categories, setCategories] = useState([]);
  const [form, setForm] = useState(null);
  const [error, setError] = useState(null);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    Promise.all([topicsApi.getTopic(id), categoriesApi.listCategories()])
      .then(([topicRes, categoriesRes]) => {
        const topic = topicRes.data;
        setForm({
          title: topic.title,
          body: topic.body,
          category_id: topic.category_id,
        });
        setCategories(categoriesRes.data);
      })
      .catch(setError);
  }, [id]);

  async function handleSubmit(e) {
    e.preventDefault();
    setError(null);
    setSubmitting(true);
    try {
      await topicsApi.updateTopic(id, form);
      navigate(`/topics/${id}`);
    } catch (err) {
      setError(err);
    } finally {
      setSubmitting(false);
    }
  }

  if (!form) return <LoadingSpinner />;

  return (
    <div className="page">
      <h1>Izmena teme</h1>
      <form onSubmit={handleSubmit} className="form">
        <label>
          Kategorija
          <select
            required
            value={form.category_id}
            onChange={(e) =>
              setForm({ ...form, category_id: e.target.value })
            }
          >
            {categories.map((category) => (
              <option key={category.id} value={category.id}>
                {category.name}
              </option>
            ))}
          </select>
        </label>
        <label>
          Naslov
          <input
            required
            maxLength={200}
            value={form.title}
            onChange={(e) => setForm({ ...form, title: e.target.value })}
          />
        </label>
        <label>
          Sadržaj
          <textarea
            required
            rows={6}
            value={form.body}
            onChange={(e) => setForm({ ...form, body: e.target.value })}
          />
        </label>
        <ErrorMessage error={error} />
        <button type="submit" disabled={submitting}>
          {submitting ? "Čuvanje…" : "Sačuvaj izmene"}
        </button>
      </form>
    </div>
  );
}
