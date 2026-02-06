import { useEffect, useRef, useCallback, useState } from 'react';

interface UseInfiniteScrollProps {
  itemsPerPage: number;
  totalItems: number;
  onLoadMore: (page: number) => void;
  threshold?: number; 
}

export function useInfiniteScroll({
  itemsPerPage,
  totalItems,
  onLoadMore,
  threshold = 0.8,
}: UseInfiniteScrollProps) {
  const [currentPage, setCurrentPage] = useState(0);
  const [isLoading, setIsLoading] = useState(false);
  const observerTarget = useRef<HTMLDivElement>(null);

  const displayedItems = currentPage * itemsPerPage + itemsPerPage;
  const hasMore = displayedItems < totalItems;

  
  const handleLoadMore = useCallback(() => {
    if (!isLoading && hasMore) {
      setIsLoading(true);
      onLoadMore(currentPage + 1);
      setCurrentPage(prev => prev + 1);
      setTimeout(() => setIsLoading(false), 300);
    }
  }, [currentPage, isLoading, hasMore, onLoadMore]);

  
  useEffect(() => {
    const observer = new IntersectionObserver(
      entries => {
        if (entries[0].isIntersecting && hasMore && !isLoading) {
          handleLoadMore();
        }
      },
      { threshold }
    );

    if (observerTarget.current) {
      observer.observe(observerTarget.current);
    }

    return () => {
      if (observerTarget.current) {
        observer.unobserve(observerTarget.current);
      }
    };
  }, [hasMore, isLoading, handleLoadMore, threshold]);

  return {
    currentPage,
    displayedItems,
    isLoading,
    hasMore,
    observerTarget,
    resetPagination: () => setCurrentPage(0),
  };
}
