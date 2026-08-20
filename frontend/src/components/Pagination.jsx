export default function Pagination({ meta, onPageChange }) {
  if (!meta || meta.last_page <= 1) return null;

  const pages = Array.from({ length: meta.last_page }, (_, i) => i + 1);

  return (
    <div className="pagination">
      <button
        type="button"
        disabled={meta.current_page <= 1}
        onClick={() => onPageChange(meta.current_page - 1)}
      >
        ‹ Prethodna
      </button>
      {pages.map((page) => (
        <button
          key={page}
          type="button"
          className={page === meta.current_page ? "active" : ""}
          onClick={() => onPageChange(page)}
        >
          {page}
        </button>
      ))}
      <button
        type="button"
        disabled={meta.current_page >= meta.last_page}
        onClick={() => onPageChange(meta.current_page + 1)}
      >
        Sledeća ›
      </button>
    </div>
  );
}
